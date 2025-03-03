<?php
namespace App\Services;
use App\Models\Customer;
use App\Models\ItemDetails;
use App\Models\Order;
use App\Models\PackageDetail;
use App\Models\PersonalInformations;
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

class ServiceDetailsService
{
    private $paypalClient;
    
    public function __construct()
    {
        // Initialize PayPal client if needed
        $paypalEnv = new SandboxEnvironment(
            config('services.paypal.client_id'),
            config('services.paypal.secret')
        );
        $this->paypalClient = new PayPalHttpClient($paypalEnv);
        
        // Initialize Stripe
        Stripe::setApiKey(config('services.stripe.secret'));
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
                'duration' => 'nullable|integer',
                'number_of_cleaners' => 'nullable|integer',
                'note' => 'nullable|string',
                'request_gender' => 'nullable|string',
                'request_language' => 'nullable|string',
                'business_property' => 'nullable|string',
                'frequency' => 'nullable|string',
                'cleaning_solvents' => 'nullable|string',
                'Equipment' => 'nullable|string',
                'personal_information' => 'array',
                'reStock_details' => 'array',
                'cleaning_item' => 'array',
                'package_details' => 'array',
                'payment_method' => 'required|in:paypal,stripe',
            ]);
            
            // Save initial data but DO NOT COMMIT yet
            $result = $this->createPendingTransaction($validatedData);
            
            // Now initiate payment based on selected method
            if ($validatedData['payment_method'] == 'paypal') {
                $paymentResponse = $this->initiatePayPalPayment($result);
                
                // Instead of committing here, we'll return the payment URL to redirect the user
                // The transaction will remain open until payment confirmation
                // We'll store details in a session or similar mechanism
                
                // Store order ID in user session or cache for later retrieval
                session(['pending_order_id' => $result['order']->order_id]);
                
                return response()->json([
                    'status' => 'pending_payment',
                    'message' => 'Please complete payment with PayPal',
                    'payment_url' => $paymentResponse['approval_url'],
                    'order_id' => $result['order']->order_id
                ]);
            } else { // Stripe
                $paymentResponse = $this->initiateStripePayment($result);
                
                // Store order ID in user session or cache for later retrieval
                session(['pending_order_id' => $result['order']->order_id]);
                
                return response()->json([
                    'status' => 'pending_payment',
                    'message' => 'Please complete payment with Stripe',
                    'payment_url' => $paymentResponse['checkout_url'],
                    'session_id' => $paymentResponse['session_id'],
                    'order_id' => $result['order']->order_id
                ]);
            }
            
            // Note: We DO NOT commit the transaction here.
            // The transaction will be committed in handlePaymentSuccess
            // or rolled back in handlePaymentCancel
            
        } catch (Exception $e) {
            // Roll back the transaction if any exception occurs
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save service details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    private function createPendingTransaction(array $validatedData)
    {
        // No need to begin transaction here, it's started in the save method
        try {
            // Handle customer creation or update
            if (!isset($validatedData['customer_id'])) {
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
                'status' => 'pending'
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
                'status' => 'pending'
            ]);
            
            // Save personal information if provided
            if (isset($validatedData['personal_information'])) {
                $personalInformation = $validatedData['personal_information'];
                $personalInformation['service_detail_id'] = $serviceDetail->id;
                PersonalInformations::create($personalInformation);
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
    
    private function initiatePayPalPayment($result)
    {
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        
        $request->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => $result['order']->order_id,
                'description' => 'Service Order',
                'amount' => [
                    'value' => $result['order']->price,
                    'currency_code' => 'USD'
                ]
            ]],
            'application_context' => [
                'cancel_url' => route('payment.cancel', ['orderId' => $result['order']->order_id]),
                'return_url' => route('payment.success', ['orderId' => $result['order']->order_id, 'method' => 'paypal']),
                'brand_name' => 'PearlySky PLC',
                'shipping_preference' => 'NO_SHIPPING',
                'user_action' => 'PAY_NOW',
            ]
        ];
        
        try {
            $response = $this->paypalClient->execute($request);
            
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
            // No need to roll back here, as the parent function will handle it
            Log::error('PayPal payment initiation failed: ' . $e->getMessage());
            throw new Exception('Failed to initialize PayPal payment: ' . $e->getMessage());
        }
    }
    
    private function initiateStripePayment($result)
    {
        $service = Service::find($result['serviceDetail']->service_id);
        
        try {
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $service->name,
                            'description' => 'Service booking on ' . $result['serviceDetail']->date,
                        ],
                        'unit_amount' => (int)($result['order']->price * 100), // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.success', ['orderId' => $result['order']->order_id, 'method' => 'stripe', 'session_id' => '{CHECKOUT_SESSION_ID}']),
                'cancel_url' => route('payment.cancel', ['orderId' => $result['order']->order_id]),
                'metadata' => [
                    'order_id' => $result['order']->order_id
                ]
            ]);
            
            // Update the payment record (but this will only be committed if the whole transaction succeeds)
            $result['payment']->update([
                'transaction_id' => $session->id,
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
                    
                    // Now we can commit the transaction because payment was successful
                    DB::commit();
                    
                    // Send confirmation email
                    $customer = Customer::find($order->customer_id);
                    $this->sendEmail($customer->email, $order->customer_id);
                    
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Payment completed successfully'
                    ]);
                } else {
                    // Payment failed or incomplete, roll back
                    DB::rollBack();
                    
                    return response()->json([
                        'status' => 'error',
                        'message' => 'PayPal payment was not completed'
                    ], 400);
                }
            } else { // Stripe
                $sessionId = $request->input('session_id');
                $session = StripeSession::retrieve($sessionId);
                
                // Only update if payment was successful
                if ($session->payment_status === 'paid') {
                    // Update payment record
                    $payment->update([
                        'status' => 'completed',
                        'transaction_id' => $session->payment_intent,
                        'payment_data' => json_encode($session)
                    ]);
                    
                    // Update order status
                    $order->update(['status' => 'active']);
                    
                    // Update service detail status
                    $serviceDetail = ServiceDetails::where('order_id', $orderId)->first();
                    $serviceDetail->update(['status' => 'confirmed']);
                    
                    // Now we can commit the transaction because payment was successful
                    DB::commit();
                    
                    // Send confirmation email
                    $customer = Customer::find($order->customer_id);
                    $this->sendEmail($customer->email, $order->customer_id);
                    
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Payment completed successfully'
                    ]);
                } else {
                    // Payment failed or incomplete, roll back
                    DB::rollBack();
                    
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Stripe payment was not completed'
                    ], 400);
                }
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
            $data = [
                'customer' => $customer,
                'order' => $latestOrder,
                'serviceDetail' => $serviceDetail,
                'service' => $service,
                'packageDetails' => $packageDetails
            ];
            
            // List of additional company email addresses
            $companyEmails = [
                'Info@Pearlyskyplc.com',
                'support@pearlyskyplc.com',
                'Recruiting@pearlyskyplc.com',
                'Sales@pearlyskyplc.com',
                'Helpdesk@pearlyskyplc.com',
                'shakilaib@pearlyskyplc.com',
                'anushatan@pearlyskyplc.com',
                'oshanhb@pearlyskyplc.com'
            ];
            
            // Send email to customer
            \Mail::to($email)->send(new \App\Mail\ServiceOrderConfirmation($data));
            Log::info("Email sent successfully to customer: {$email}");
            
            // Send the same email to all company email addresses
            \Mail::to($companyEmails)->send(new \App\Mail\ServiceOrderConfirmation($data));
            Log::info("Email sent successfully to company emails: " . implode(', ', $companyEmails));
            
            return true;
        } catch (Exception $e) {
            Log::error("Failed to send email. Error: " . $e->getMessage());
            return false;
        }
    }
}