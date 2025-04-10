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
use Egulias\EmailValidator\Result\ValidEmail;
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
use App\Http\Controllers\TranslationController;

class ServiceDetailsService
{
    private $paypalClient;
    private $country;

    public function __construct(Request $request)
    {
        $this->country = $request->query('country', 'EN');
        
        try {
            $clientId = config('services.paypal.client_id');
            $clientSecret = config('services.paypal.secret');

            if (empty($clientId) || empty($clientSecret)) {
                throw new Exception('PayPal credentials are not properly configured');
            }

            $paypalEnv = new SandboxEnvironment($clientId, $clientSecret);
            $this->paypalClient = new PayPalHttpClient($paypalEnv);

            $stripeKey = config('services.stripe.secret');
            if (empty($stripeKey)) {
                throw new Exception('Stripe is not properly configured');
            }
            Stripe::setApiKey($stripeKey);
        } catch (Exception $e) {
            Log::error('Payment gateway initialization failed: ' . $e->getMessage());
        }
    }

    public function save(Request $request)
    {
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

          
                $result = $this->createPendingTransaction($validatedData);
                $result['country'] = $this->country;

                if ($validatedData['payment_method'] == 'paypal') {
                    $paymentResponse = $this->initiatePayPalPayment($result);
                    session(['pending_order_id' => $result['order']->order_id]);

                    return response()->json([
                        'status' => 'pending_payment',
                        'message' => 'Please complete payment with PayPal',
                        'payment_url' => $paymentResponse['approval_url'],
                        'order_id' => $result['order']->order_id
                    ]);
                } else if ($validatedData['payment_method'] == 'stripe' || $validatedData['payment_method'] == 'card') {
                    $paymentResponse = $this->initiateStripePayment($result);
                    session(['pending_order_id' => $result['order']->order_id]);

                    return response()->json([
                        'status' => 'pending_payment',
                        'message' => 'Please complete payment with your card',
                        'payment_url' => $paymentResponse['checkout_url'],
                        'session_id' => $paymentResponse['session_id'],
                        'order_id' => $result['order']->order_id
                    ]);
                } else {
                    return $this->completeDirectPayment($result, $validatedData['payment_method']);
                }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Service details save failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save service details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function createPendingTransaction(array $validatedData)
    {
        try {
            if (isset($validatedData['customer'])) {
                $validatedData['customer'] = TranslationController::translateJson(
                    $validatedData['customer'],
                    $this->country,
                    true
                );
            }

            if (!isset($validatedData['customer_id'])) {
                $existingCustomer = Customer::where('email', $validatedData['customer']['email'])->first();

                if ($existingCustomer) {
                    $validatedData['customer']['password'] = Hash::make($existingCustomer->password);
                    $validatedData['customer']['customer_id'] = $existingCustomer->customer_id;
                    $existingCustomer->update($validatedData['customer']);
                    $customer = $existingCustomer;
                    $customerId = $existingCustomer->customer_id;
                } else {
                    $validatedData['customer']['password'] = Hash::make($validatedData['customer']['password']);
                    $customer = Customer::create($validatedData['customer']);
                    $customerId = $customer->customer_id;
                }

            } else {
                $customer = Customer::find($validatedData['customer_id']);
                $validatedData['customer']['password'] = Hash::make($customer->password);
                $customer->update($validatedData['customer']);
                $customerId = $validatedData['customer_id'];
            }

            $order = Order::create([
                'customer_id' => $customerId,
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'price' => $validatedData['price'],
                'status' => 'pending',
                'qr_code' => null
            ]);

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

            if (isset($validatedData['free_estimate'])) {
                foreach ($validatedData['free_estimate'] as $data) {
                    $reStockingItems = ReStockingChecklist::where('category', $data)->get();
                    foreach ($reStockingItems as $item) {
                        ReStockingChecklistDetails::create([
                            're_stocking_checklist_id' => $item->id,
                            'service_detail_id' => $serviceDetail->id,
                        ]);
                    }
                }
            }

            if (isset($validatedData['personal_information'])) {
                $translatedPersonalInfo = TranslationController::translateJson(
                    $validatedData['personal_information'],
                    $this->country,
                    true
                );
                foreach ($translatedPersonalInfo as $info) {
                    PersonalInformations::create([
                        'service_detail_id' => $serviceDetail->id,
                        ...$info
                    ]);
                }
            }

            if (isset($validatedData['package_details'])) {
                $translatedPackages = TranslationController::translateJson(
                    $validatedData['package_details'],
                    $this->country,
                    true
                );
                foreach ($translatedPackages as $package) {
                    PackageDetail::create([
                        'package_id' => $package['package_id'],
                        'service_detail_id' => $serviceDetail->id,
                        'price' => $package['price'] ?? null,
                        'qty' => $package['qty'] ?? null,
                    ]);
                }
            }

            if (isset($validatedData['reStock_details'])) {
                foreach ($validatedData['reStock_details'] as $reStockDetail) {
                    ReStockingChecklistDetails::create([
                        're_stocking_checklist_id' => $reStockDetail['re_stocking_checklist_id'],
                        'service_detail_id' => $serviceDetail->id,
                    ]);
                }
            }

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

            $payment = Payment::create([
                'order_id' => $order->order_id,
                'amount' => $validatedData['price'],
                'payment_method' => $validatedData['payment_method'],
                'status' => 'pending',
                'country' => $this->country
            ]);

            return [
                'customer' => $customer,
                'customerId' => $customerId,
                'order' => $order,
                'serviceDetail' => $serviceDetail,
                'payment' => $payment,
                'country' => $this->country
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function completeDirectPayment($result, $paymentMethod)
    {
        try {
            $result['payment']->update([
                'status' => 'completed',
                'payment_method' => $paymentMethod,
                'transaction_id' => 'DIRECT-' . time() . '-' . $result['order']->order_id,
                'payment_data' => json_encode(['method' => $paymentMethod, 'date' => now()])
            ]);

            $result['order']->update(['status' => 'active']);
            $result['serviceDetail']->update(['status' => 'confirmed']);

            $qrCodePath = $this->generateQrCode($result['order']->order_id);
            $result['order']->update(['qr_code' => $qrCodePath]);
            

            DB::commit();

            $this->sendTranslatedEmail(
                $result['customer']->email, 
                $result['customerId'],
                $result['country']
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Order completed successfully with ' . $paymentMethod,
                'order_id' => $result['order']->order_id,
                'qr_code' => $qrCodePath
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to complete direct payment: ' . $e->getMessage());
        }
    }

    private function generateQrCode($orderId)
    {
        try {
            $url = route('orders.show', $orderId);
            $qrCode = new QrCode($url);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            $image = "order". $orderId . '.jpg';
            $filePath = 'qr-codes/' . $image;

            Storage::disk('public')->put($filePath, $result->getString());
            Order::where('order_id', $orderId)->update(['qr_code' => $image]);
            
            return $filePath;
        } catch (Exception $e) {
            Log::error('Failed to generate QR code: ' . $e->getMessage());
            return null;
        }
    }

    private function initiatePayPalPayment($result)
    {
        try {
            if (!$this->paypalClient) {
                throw new Exception('PayPal client is not initialized.');
            }

            $request = new OrdersCreateRequest();
            $request->prefer('return=representation');

            $price = preg_replace('/[^0-9.]/', '', $result['order']->price);

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
                    'return_url' => route('payment.success', [
                        'orderId' => $result['order']->order_id, 
                        'method' => 'paypal',
                        'country' => $result['country']
                    ]),
                    'brand_name' => 'PearlySky PLC',
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'PAY_NOW',
                ]
            ];

            $response = $this->paypalClient->execute($request);

            $approvalUrl = null;
            foreach ($response->result->links as $link) {
                if ($link->rel === 'approve') {
                    $approvalUrl = $link->href;
                    break;
                }
            }

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
            Log::error('PayPal payment initiation failed: ' . $e->getMessage());
            throw new Exception('Failed to initialize PayPal payment: ' . $e->getMessage());
        }
    }

    private function initiateStripePayment($result)
    {
        $service = Service::find($result['serviceDetail']->service_id);

        try {
            $price = preg_replace('/[^0-9.]/', '', $result['order']->price);
            $priceInCents = (int)(floatval($price) * 100);
            
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => $service->name,
                                'description' => 'Service booking on ' . $result['serviceDetail']->date,
                                'images' => [],
                            ],
                            'unit_amount' => $priceInCents,
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'success_url' => route('payment.success', [
                    'orderId' => $result['order']->order_id, 
                    'method' => 'stripe', 
                    'session_id' => '{CHECKOUT_SESSION_ID}',
                    'country' => $result['country']
                ]),
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
                'customer_email' => $result['customer']->email ?? null,
                'locale' => 'auto',
                'allow_promotion_codes' => true,
                'billing_address_collection' => 'required',
            ]);

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
            Log::error('Stripe payment initiation failed: ' . $e->getMessage());
            throw new Exception('Failed to initialize Stripe payment: ' . $e->getMessage());
        }
    }

    private function sendTranslatedEmail($email, $customerId, $country = 'EN')
    {
        try {
            $customer = Customer::find($customerId);
            $order = Order::where('customer_id', $customerId)->latest()->first();
            $serviceDetail = ServiceDetails::where('order_id', $order->order_id)->first();
            $service = Service::find($serviceDetail->service_id);
            
            $packageDetails = PackageDetail::where('service_detail_id', $serviceDetail->id)
                ->with('package')
                ->get()
                ->toArray();

                $payment=Payment::where('order_id', $order->order_id)->first();

            $translatedPackages = TranslationController::translateJson($packageDetails, $country);
            $translateCustomer=TranslationController::translateJson($customer->toArray(), $country);
            $translateService=TranslationController::translateJson($service->toArray(), $country);
            $translateOrder=TranslationController::translateJson($order->toArray(), $country);
            $translateServiceDetail=TranslationController::translateJson($serviceDetail->toArray(), $country);
            $translatePayment=TranslationController::translateJson($payment->toArray(), $country);


            $data = [
                'customer' => $translateCustomer,
                'order' => $translateOrder,
                'service' => $translateService,
                'packageDetails' => $translatedPackages,
                'qr_image' => "order" . $order->order_id . '.jpg',
                'language' => $country,
                'serviceDetail' => $translateServiceDetail,
                'payment' => $translatePayment,
            ];


            \Mail::to($email)->send(new \App\Mail\ServiceOrderConfirmation(
                $data,
                storage_path('app/public/' . $order->qr_code)
            ));

            if ($country !== 'EN') {
                $englishData = $data;
                $englishData['customer'] = $customer->toArray();
                $englishData['service'] = $service->toArray();
                $englishData['packageDetails'] = $packageDetails;
                $englishData['language'] = 'EN';
            }

            $companyEmails = [
                'Info@Pearlyskyplc.com',
                'support@pearlyskyplc.com',
                'Sales@pearlyskyplc.com',
                'Helpdesk@pearlyskyplc.com',
                'shakilaib@pearlyskyplc.com',
                'anushatan@pearlyskyplc.com',
                'oshanhb@pearlyskyplc.com',
                'systempearlyskycleaningplc@gmail.com'
            ];
            
            // foreach ($companyEmails as $companyEmail) {
            //     \Mail::to($companyEmail)->send(new \App\Mail\ServiceOrderConfirmation(
            //         $data,
            //         storage_path('app/public/' . $order->qr_code)
            //     ));
            // }

            return true;
        } catch (Exception $e) {
            Log::error("Email sending failed: " . $e->getMessage());
            return false;
        }
    }

    public function handlePaymentSuccess(Request $request, $orderId, $method)
    {
        DB::beginTransaction();

        try {
            $order = Order::findOrFail($orderId);
            $payment = Payment::where('order_id', $orderId)->firstOrFail();
            $country = $payment->country ?? 'EN';

            if ($method === 'paypal') {
                $paypalOrderId = $request->input('token');
                $request = new OrdersCaptureRequest($paypalOrderId);
                $response = $this->paypalClient->execute($request);

                if ($response->result->status === 'COMPLETED') {
                    $payment->update([
                        'status' => 'completed',
                        'transaction_id' => $response->result->id,
                        'payment_data' => json_encode($response->result)
                    ]);
                } else {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'PayPal payment was not completed'
                    ], 400);
                }
            } elseif ($method === 'stripe' || $method === 'card') {
                $sessionId = $request->input('session_id');
                $session = StripeSession::retrieve($sessionId);

                if ($session->payment_status === 'paid') {
                    $payment->update([
                        'status' => 'completed',
                        'payment_method' => 'card',
                        'transaction_id' => $session->payment_intent,
                        'payment_data' => json_encode($session)
                    ]);
                } else {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Card payment was not completed'
                    ], 400);
                }
            } else {
                $payment->update([
                    'status' => 'completed',
                    'transaction_id' => 'DIRECT-' . time() . '-' . $orderId,
                    'payment_data' => json_encode(['method' => $method, 'date' => now()])
                ]);
            }

            $order->update(['status' => 'active']);
            ServiceDetails::where('order_id', $orderId)->update(['status' => 'confirmed']);

            $qrCodePath = $this->generateQrCode($orderId);
            $order->update(['qr_code' => $qrCodePath]);

            DB::commit();

            $customer = Customer::find($order->customer_id);
            $this->sendTranslatedEmail($customer->email, $order->customer_id, $country);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment completed successfully',
                'qr_code' => $qrCodePath
            ]);
        } catch (Exception $e) {
            DB::rollBack();
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
}