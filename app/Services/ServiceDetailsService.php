<?php
namespace App\Services;

use App\Models\Customer;
use App\Models\ItemDetails;
use App\Models\Order;
use App\Models\PackageDetail;
use App\Models\PersonalInformations;
use App\Models\ReStockingChecklist;
use App\Models\ReStockingChecklistDetails;
use App\Models\Service;
use App\Models\ServiceDetails;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class ServiceDetailsService
{
    private $paypalClient;

    public function __construct()
    {
        try {
            // Initialize PayPal client with proper error handling
            $clientId = config('services.paypal.client_id');
            $clientSecret = config('services.paypal.secret');

            // Log the initialization attempt (but not the credentials)
            Log::info('Initializing PayPal client');

            // Check if credentials exist
            if (empty($clientId) || empty($clientSecret)) {
                Log::error('PayPal credentials are missing or empty');
                throw new Exception('PayPal credentials are not properly configured');
            }

            $paypalEnv = new SandboxEnvironment($clientId, $clientSecret);
            $this->paypalClient = new PayPalHttpClient($paypalEnv);

            // Initialize Stripe with proper error handling
            $stripeKey = config('services.stripe.secret');
            if (empty($stripeKey)) {
                Log::error('Stripe key is missing or empty');
                throw new Exception('Stripe is not properly configured');
            }

            Stripe::setApiKey($stripeKey);
        } catch (Exception $e) {
            Log::error('Payment gateway initialization failed: ' . $e->getMessage());
            // Don't throw here, let the service function handle errors when methods are called
        }
    }

    public function save(Request $request)
    {
        // Start transaction at the outermost level
        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'customer_id' => 'sometimes',
                'customer' => 'required_without:customer_id|array',
                'service_id' => 'required',
                'price' => 'required|string',
                'date' => 'required|date',
                'time' => 'required',
                'property_size' => 'nullable|string',
                'duration' => 'nullable|string',
                'number_of_cleaners' => 'nullable|integer',
                'note' => 'nullable|string',
                'request_gender' => 'nullable|string',
                'request_language' => 'nullable|string',
                'business_property' => 'nullable|string',
                'frequency' => 'nullable|string',
                'cleaning_solvents' => 'nullable|string',
                'Equipment' => 'nullable|string',
                'special_request' => 'nullable|string',

                'chemical' => 'nullable|string',
                'things_to_clean' => 'nullable|string',
                'location_from' => 'nullable|string',
                'location_to' => 'nullable|string',
                'materials' => 'nullable|string',
                'options_type' => 'nullable|string',
                'pressure_washing_type' => 'nullable|string',
                'event_type' => 'nullable|string',
                'pool_type' => 'nullable|string',

                'free_estimate' => 'nullable|array',
                'time_zoon' => 'nullable|string',
                'personal_information' => 'array',
                'reStock_details' => 'array',
                'cleaning_item' => 'array',
                'package_details' => 'array',
                'payment_method' => 'required|string',
            ]);

            // Save initial data but DO NOT COMMIT yet
            $result = $this->createPendingTransaction($validatedData);

            // Now initiate payment based on selected method
            if ($validatedData['payment_method'] == 'paypal') {
                // Check if PayPal client is properly initialized
                if (!$this->paypalClient) {
                    throw new Exception('PayPal client is not properly initialized. Check your credentials.');
                }

                $paymentResponse = $this->initiatePayPalPayment($result);

                // Store order ID in user session or cache for later retrieval
                session(['pending_order_id' => $result['order']->order_id]);

                return response()->json([
                    'status' => 'pending_payment',
                    'message' => 'Please complete payment with PayPal',
                    'payment_url' => $paymentResponse['approval_url'],
                    'order_id' => $result['order']->order_id
                ]);
            } else if ($validatedData['payment_method'] == 'stripe' || $validatedData['payment_method'] == 'card') { // Stripe or card payments
                $paymentResponse = $this->initiateStripePayment($result);

                // Store order ID in user session or cache for later retrieval
                session(['pending_order_id' => $result['order']->order_id]);

                return response()->json([
                    'status' => 'pending_payment',
                    'message' => 'Please complete payment with your card',
                    'payment_url' => $paymentResponse['checkout_url'],
                    'session_id' => $paymentResponse['session_id'],
                    'order_id' => $result['order']->order_id
                ]);
            } else {
                // Direct completion for other payment methods
                return $this->completeDirectPayment($result, $validatedData['payment_method']);
            }

        } catch (Exception $e) {
            // Roll back the transaction if any exception occurs
            DB::rollBack();

            Log::error('Service details save failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save service details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete order directly for non-PayPal/Stripe payment methods
     * 
     * @param array $result Result from createPendingTransaction
     * @param string $paymentMethod The payment method used
     * @return \Illuminate\Http\JsonResponse
     */
    private function completeDirectPayment($result, $paymentMethod)
    {
        try {
            // Update payment record to completed
            $result['payment']->update([
                'status' => 'completed',
                'payment_method' => $paymentMethod,
                'transaction_id' => 'DIRECT-' . time() . '-' . $result['order']->order_id,
                'payment_data' => json_encode(['method' => $paymentMethod, 'date' => now()])
            ]);

            // Update order status
            $result['order']->update(['status' => 'active']);

            // Update service detail status
            $result['serviceDetail']->update(['status' => 'confirmed']);

            // Generate QR code for the work order
            $qrCodePath = $this->generateQrCode($result['order']->order_id);
            
            // Update the order with QR code path
            $result['order']->update(['qr_code' => $qrCodePath]);

            // Now we can commit the transaction
            DB::commit();

            // Send confirmation email
            $this->sendEmail($result['customer']->email, $result['customerId']);

            return response()->json([
                'status' => 'success',
                'message' => 'Order completed successfully with ' . $paymentMethod,
                'order_id' => $result['order']->order_id,
                'qr_code' => $qrCodePath
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Direct payment completion failed: ' . $e->getMessage());

            throw new Exception('Failed to complete direct payment: ' . $e->getMessage());
        }
    }

    private function createPendingTransaction(array $validatedData)
    {
        // No need to begin transaction here, it's started in the save method
        try {
            // Handle customer creation or update
            if (!isset($validatedData['customer_id'])) {
                $validatedData['customer']['password'] = Hash::make($validatedData['customer']['password']);
                $customer = Customer::create($validatedData['customer']);
                $result = DB::table('customers')
                    ->Where('email', 'LIKE', '%' . $customer->email . '%')
                    ->get()->first();
                $customerId = $result->customer_id;
            } else {
                $customer = Customer::find($validatedData['customer_id']);
                $customer->update($validatedData['customer']);
                $customerId = $validatedData['customer_id'];
            }

            // Create order
            $order = Order::create([
                'customer_id' => $customerId,
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'price' => ($validatedData['price']),
                'status' => 'pending',
                'qr_code' => null // Initially set QR code to null
            ]);

            // Create service detail
            $serviceDetail = ServiceDetails::create([
                'order_id' => $order->order_id,
                'customer_id' => $customerId,
                'service_id' => $validatedData['service_id'],
                'price' => $validatedData['price'],
                'date' => $validatedData['date'],
                'time' => $validatedData['time'],
                'property_size' => $validatedData['property_size'] ?? null,
                'duration' => $validatedData['duration'] ?? null,
                'number_of_cleaners' => $validatedData['number_of_cleaners'] ?? 1,
                'note' => $validatedData['note'] ?? null,
                'request_gender' => $validatedData['request_gender'] ?? null,
                'frequency' => $validatedData['frequency'] ?? null,
                'request_language' => $validatedData['request_language'] ?? 'en',
                'business_property' => $validatedData['business_property'] ?? null,
                'cleaning_solvents' => $validatedData['cleaning_solvents'] ?? null,
                'Equipment' => $validatedData['Equipment'] ?? null,
                'time_zoon' => $validatedData['time_zoon'] ?? null,
                'special_request' => $validatedData['special_request'] ?? null,

                'chemical' => $validatedData['chemical'] ?? null,
                'things_to_clean' => $validatedData['things_to_clean'] ?? null,
                'location_from' => $validatedData['location_from'] ?? null,
                'location_to' => $validatedData['location_to'] ?? null,
                'materials' => $validatedData['materials'] ?? null,
                'options_type' => $validatedData['options_type'] ?? null,
                'pressure_washing_type' => $validatedData['pressure_washing_type'] ?? null,
                'event_type' => $validatedData['event_type'] ?? null,
                'pool_type' => $validatedData['pool_type'] ?? null,
                'status' => 'pending'
            ]);

            Log::info("message");
            // Save personal information if provided
            if (isset($validatedData['free_estimate'])) {
                foreach ($validatedData['free_estimate'] as $data) {
                    Log::info($data);
                    $reStockingItems = ReStockingChecklist::where('category', $data)->get();

                    // Create entries in ReStockingChecklistDetails for each found item
                    foreach ($reStockingItems as $item) {
                        ReStockingChecklistDetails::create([
                            're_stocking_checklist_id' => $item->id,
                            'service_detail_id' => $serviceDetail->id,
                        ]);
                    }
                }
                
            }

            // Save personal information if provided
            if (isset($validatedData['personal_information'])) {
                foreach ($validatedData['personal_information'] as $personalInformation) {
                    $personalInformation['service_detail_id'] = $serviceDetail->id;
                    PersonalInformations::create($personalInformation);
                }
            }

            // Save package details if provided
            if (isset($validatedData['package_details'])) {
                foreach ($validatedData['package_details'] as $packageDetail) {
                    PackageDetail::create([
                        'package_id' => $packageDetail['package_id'],
                        'service_detail_id' => $serviceDetail->id,
                        'price' => $packageDetail['price'] ?? null,
                        'qty' => $packageDetail['qty'] ?? null,
                    ]);
                }
            }

            // Save reStock details if provided
            if (isset($validatedData['reStock_details'])) {
                foreach ($validatedData['reStock_details'] as $reStockDetail) {
                    ReStockingChecklistDetails::create([
                        're_stocking_checklist_id' => $reStockDetail['re_stocking_checklist_id'],
                        'service_detail_id' => $serviceDetail->id,
                    ]);
                }
            }

            // Save item details if provided
            if (isset($validatedData['cleaning_item'])) {
                foreach ($validatedData['cleaning_item'] as $item) {
                    ItemDetails::create([
                        'item_id' => $item['id'],
                        'service_detail_id' => $serviceDetail->id,
                        'qty' => $item['qty'],
                        'price' => $item['price']
                    ]);
                }
            }

            // Create a payment record
            $payment = Payment::create([
                'order_id' => $order->order_id,
                'amount' => $validatedData['price'],
                'payment_method' => $validatedData['payment_method'],
                'status' => 'pending'
            ]);

            // DO NOT COMMIT transaction here!
            // We'll commit only after payment is successful

            return [
                'customer' => $customer,
                'customerId' => $customerId,
                'order' => $order,
                'serviceDetail' => $serviceDetail,
                'payment' => $payment
            ];
        } catch (Exception $e) {
            // No need to roll back here, as the parent function will handle it
            throw $e;
        }
    }

  
    private function generateQrCode($orderId)
    {
        try {
            Log::info('Generating QR code for order ID: ' . $orderId);
            
            // Generate QR code content - could be a URL to view the order details
            $url = route('orders.show', $orderId);
            
            // Log the generated URL
            Log::info('Generated URL for QR code: ' . $url);
            
            // Create a QR code instance
            $qrCode = new QrCode($url);
            
            // Save the QR code as a PNG file
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            $image="order". $orderId . '.jpg';
            
            // Define the file path
            $filePath = 'qr-codes/' . $image;

            
            // Save the QR code to the storage disk
            Storage::disk('public')->put($filePath, $result->getString());
            Order::where('order_id', $orderId)->update(['qr_code' => $image]);
            
            return $filePath;
        } catch (Exception $e) {
            Log::error('Failed to generate QR code: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString()); // Log the stack trace for more details
            return null;
        }
    }

    private function initiatePayPalPayment($result)
    {
        try {
            // Double-check PayPal client initialization
            if (!$this->paypalClient) {
                throw new Exception('PayPal client is not initialized. Please check your credentials in the .env file.');
            }

            $request = new OrdersCreateRequest();
            $request->prefer('return=representation');

            // Clean the price value to ensure it's a valid number format without currency symbols
            $price = preg_replace('/[^0-9.]/', '', $result['order']->price);

            // Log request data (excluding sensitive information)
            Log::info('Creating PayPal order for Order ID: ' . $result['order']->order_id . ' with amount: ' . $result['order']->price);

            $request->body = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $result['order']->order_id,
                        'description' => 'Service Order',
                        'amount' => [
                            'value' => $price,
                            'currency_code' => 'USD'
                        ]
                    ]
                ],
                'application_context' => [
                    'cancel_url' => route('payment.cancel', ['orderId' => $result['order']->order_id]),
                    'return_url' => route('payment.success', ['orderId' => $result['order']->order_id, 'method' => 'paypal']),
                    'brand_name' => 'PearlySky PLC',
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'PAY_NOW',
                ]
            ];

            $response = $this->paypalClient->execute($request);

            // Log successful response (excluding sensitive parts)
            Log::info('PayPal order created successfully. PayPal order ID: ' . $response->result->id);

            // Extract approval URL to redirect user to PayPal
            $approvalUrl = null;
            foreach ($response->result->links as $link) {
                if ($link->rel === 'approve') {
                    $approvalUrl = $link->href;
                    break;
                }
            }

            // Update the payment record (but this will only be committed if the whole transaction succeeds)
            $result['payment']->update([
                'transaction_id' => $response->result->id,
                'payment_data' => json_encode($response->result)
            ]);

            return [
                'success' => true,
                'approval_url' => $approvalUrl,
                'paypal_order_id' => $response->result->id
            ];
        } catch (Exception $e) {
            // Log the error with detailed information
            Log::error('PayPal payment initiation failed: ' . $e->getMessage());

            // Check for specific PayPal error messages
            $errorMsg = $e->getMessage();
            if (strpos($errorMsg, 'invalid_client') !== false) {
                throw new Exception('Failed to initialize PayPal payment: Invalid client credentials. Please check your PayPal Client ID and Secret in your .env file.');
            } else {
                throw new Exception('Failed to initialize PayPal payment: ' . $e->getMessage());
            }
        }
    }

    private function initiateStripePayment($result)
    {
        $service = Service::find($result['serviceDetail']->service_id);

        try {
            // Clean the price value to ensure it's a valid number format without currency symbols
            $price = preg_replace('/[^0-9.]/', '', $result['order']->price);
            
            // Convert to float first, then to integer cents
            $priceInCents = (int)(floatval($price) * 100);
            
            $session = StripeSession::create([
                'payment_method_types' => ['card'], // Explicitly specify card as the payment method
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => $service->name,
                                'description' => 'Service booking on ' . $result['serviceDetail']->date,
                                'images' => [], // You can add product images if available
                            ],
                            'unit_amount' => $priceInCents, // Use the properly sanitized and converted price
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'success_url' => route('payment.success', ['orderId' => $result['order']->order_id, 'method' => 'stripe', 'session_id' => '{CHECKOUT_SESSION_ID}']),
                'cancel_url' => route('payment.cancel', ['orderId' => $result['order']->order_id]),
                'metadata' => [
                    'order_id' => $result['order']->order_id,
                    'customer_id' => $result['customerId'],
                    'service_id' => $result['serviceDetail']->service_id
                ],
                'payment_intent_data' => [
                    'description' => 'Order #' . $result['order']->order_id . ' - ' . $service->name,
                    'metadata' => [
                        'order_id' => $result['order']->order_id,
                        'customer_id' => $result['customerId'],
                        'service_name' => $service->name
                    ]
                ],
                'customer_email' => $result['customer']->email ?? null, // Pre-fill customer email if available
                'locale' => 'auto', // Auto-detect the user's preferred language
                'allow_promotion_codes' => true, // Enable promotion codes if you want to use them
                'billing_address_collection' => 'required', // Collect billing address for better fraud protection
            ]);

            // Update the payment record (but this will only be committed if the whole transaction succeeds)
            $result['payment']->update([
                'transaction_id' => $session->id,
                'payment_method' => 'card',
                'payment_data' => json_encode($session)
            ]);

            return [
                'success' => true,
                'checkout_url' => $session->url,
                'session_id' => $session->id
            ];
        } catch (Exception $e) {
            // No need to roll back here, as the parent function will handle it
            Log::error('Stripe payment initiation failed: ' . $e->getMessage());
            throw new Exception('Failed to initialize Stripe payment: ' . $e->getMessage());
        }
    }

    public function handlePaymentSuccess(Request $request, $orderId, $method)
    {
        // Retrieve and resume the transaction
        DB::beginTransaction();

        try {
            $order = Order::findOrFail($orderId);
            $payment = Payment::where('order_id', $orderId)->firstOrFail();

            if ($method === 'paypal') {
                // Capture the PayPal payment
                $paypalOrderId = $request->input('token'); // PayPal returns the order ID as 'token'
                $request = new OrdersCaptureRequest($paypalOrderId);
                $response = $this->paypalClient->execute($request);

                // Only update if payment was successful
                if ($response->result->status === 'COMPLETED') {
                    // Update payment record
                    $payment->update([
                        'status' => 'completed',
                        'transaction_id' => $response->result->id,
                        'payment_data' => json_encode($response->result)
                    ]);

                    // Update order status
                    $order->update(['status' => 'active']);

                    // Update service detail status
                    $serviceDetail = ServiceDetails::where('order_id', $orderId)->first();
                    $serviceDetail->update(['status' => 'confirmed']);

                    // Generate QR code for the work order
                    $qrCodePath = $this->generateQrCode($orderId);
                    
                    // Update the order with QR code path
                    $order->update(['qr_code' => $qrCodePath]);

                    // Now we can commit the transaction because payment was successful
                    DB::commit();

                    // Send confirmation email
                    $customer = Customer::find($order->customer_id);
                    $this->sendEmail($customer->email, $order->customer_id);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Payment completed successfully',
                        'qr_code' => $qrCodePath
                    ]);
                } else {
                    // Payment failed or incomplete, roll back
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => 'PayPal payment was not completed'
                    ], 400);
                }
            } elseif ($method === 'stripe' || $method === 'card') { // Stripe or card payments
                $sessionId = $request->input('session_id');
                $session = StripeSession::retrieve($sessionId);

                // Only update if payment was successful
                if ($session->payment_status === 'paid') {
                    // Update payment record
                    $payment->update([
                        'status' => 'completed',
                        'payment_method' => 'card',
                        'transaction_id' => $session->payment_intent,
                        'payment_data' => json_encode($session)
                    ]);

                    // Update order status
                    $order->update(['status' => 'active']);

                    // Update service detail status
                    $serviceDetail = ServiceDetails::where('order_id', $orderId)->first();
                    $serviceDetail->update(['status' => 'confirmed']);

                    // Generate QR code for the work order
                    $qrCodePath = $this->generateQrCode($orderId);
                    
                    // Update the order with QR code path
                    $order->update(['qr_code' => $qrCodePath]);

                    // Now we can commit the transaction because payment was successful
                    DB::commit();

                    // Send confirmation email
                    $customer = Customer::find($order->customer_id);
                    $this->sendEmail($customer->email, $order->customer_id);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Card payment completed successfully',
                        'qr_code' => $qrCodePath
                    ]);
                } else {
                    // Payment failed or incomplete, roll back
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Card payment was not completed'
                    ], 400);
                }
            } else {
                // Direct payment completion
                // Update payment record
                $payment->update([
                    'status' => 'completed',
                    'transaction_id' => 'DIRECT-' . time() . '-' . $orderId,
                    'payment_data' => json_encode(['method' => $method, 'date' => now()])
                ]);

                // Update order status
                $order->update(['status' => 'active']);

                // Update service detail status
                $serviceDetail = ServiceDetails::where('order_id', $orderId)->first();
                $serviceDetail->update(['status' => 'confirmed']);

                // Generate QR code for the work order
                $qrCodePath = $this->generateQrCode($orderId);
                
                // Update the order with QR code path
                $order->update(['qr_code' => $qrCodePath]);

                // Now we can commit the transaction
                DB::commit();

                // Send confirmation email
                $customer = Customer::find($order->customer_id);
                $this->sendEmail($customer->email, $order->customer_id);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Order completed successfully with ' . $method,
                    'qr_code' => $qrCodePath
                ]);
            }

        } catch (Exception $e) {
            // Roll back on any exception
            DB::rollBack();

            Log::error('Payment completion failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to complete payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function handlePaymentCancel($orderId)
    {
        DB::rollBack();

        try {
            return response()->json([
                'status' => 'cancelled',
                'message' => 'Payment was cancelled'
            ]);
        } catch (Exception $e) {
            Log::error('Error handling payment cancellation: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process payment cancellation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function sendEmail($email, $customerId)
    {
        try {
            $customer = Customer::find($customerId);
            $latestOrder = Order::where('customer_id', $customerId)->latest()->first();
            $serviceDetail = ServiceDetails::where('order_id', $latestOrder->order_id)->first();
            $service = Service::find($serviceDetail->service_id);
            
            // Get package details
            $packageDetails = PackageDetail::where('service_detail_id', $serviceDetail->id)
                ->with('package')
                ->get();
                
            // Get the QR code URL for the email
            $qrCodeUrl = null;
            if ($latestOrder->qr_code) {
                // Check if the QR code file exists
                if (Storage::disk('public')->exists($latestOrder->qr_code)) {
                    // Get the correct public URL for the QR code
                    $qrCodeUrl = asset('storage/' . $latestOrder->qr_code);
                    Log::info("QR code URL: {$qrCodeUrl}");
                } else {
                    Log::warning("QR code file not found: {$latestOrder->qr_code}");
                }
            } else {
                Log::warning("No QR code path saved for order: {$latestOrder->order_id}");
            }

            $image = "order" . $latestOrder->order_id . '.jpg';
            
            $data = [
                'customer' => $customer,
                'order' => $latestOrder,
                'qr_image'=> $image,
                'serviceDetail' => $serviceDetail,
                'service' => $service,
                'packageDetails' => $packageDetails
            ];

            Log::info("ss data: " . json_encode($data));

            // List of additional company email addresses
            $companyEmails = [
                'Info@Pearlyskyplc.com',
                'support@pearlyskyplc.com',
                'Recruiting@pearlyskyplc.com',
                'Sales@pearlyskyplc.com',
                'Helpdesk@pearlyskyplc.com',
                'shakilaib@pearlyskyplc.com',
                'anushatan@pearlyskyplc.com',
                'oshanhb@pearlyskyplc.com',
                'systempearlyskycleaningplc@gmail.com',
                'hrdreport@pearlyskyplc.com'
            ];
            
            \Mail::to($email)->send(new \App\Mail\ServiceOrderConfirmation($data, storage_path('app/public/' . $latestOrder->qr_code)));
            Log::info("Sending data: " . json_encode($data));
            
            // Send email to customer with QR code attachment
            \Mail::to($email)->send(new \App\Mail\ServiceOrderConfirmation($data, storage_path('app/public/' . $latestOrder->qr_code)));
            Log::info("Email sent successfully to company emails: " . implode(', ', $companyEmails));

            return true;
        } catch (Exception $e) {
            Log::error("Failed to send email. Error: " . $e->getMessage());
            return false;
        }
    }
}