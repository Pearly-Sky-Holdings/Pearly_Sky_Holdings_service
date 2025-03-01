<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\ItemDetails;
use App\Models\Order;
use App\Models\PackageDetail;
use App\Models\PersonalInformations;
use App\Models\Payment;
use App\Models\ReStockingChecklistDetails;
use App\Models\Service;
use App\Models\ServiceDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class ServiceDetailsService
{
    public function save(Request $request)
    {
        try {
            Log::info('Service details saving process started');
            $validatedData = $request->validate([
                'customer_id' => 'sometimes',
                'customer' => 'required_without:customer_id|array',
                'service_id' => 'required',
                'price' => 'required|numeric',
                'date' => 'required|date',
                'time' => 'required',
                'property_size' => 'nullable|string',
                'duration' => 'nullable|integer',
                'number_of_cleaners' => 'nullable|integer',
                'note' => 'nullable|string',
                'request_gender' => 'nullable|string',
                'request_language' => 'nullable|string',
                'business_property' => 'nullable|string',
                'cleaning_solvents' => 'nullable|string',
                'Equipment' => 'nullable|string',
                'total_price' => 'nullable|string',
                'personal_information' => 'array',
                'reStock_details' => 'array',
                'cleaning_item' => 'array',
                'package_details' => 'array',
                'payment_method' => 'required|in:paypal,stripe,cash', // අලුත්වෙන් එකතු කළා
            ]);

            // Store order details in session for later use after payment
            $orderData = $validatedData;
            session(['order_data' => $orderData]);

            // Based on payment method, redirect to appropriate payment gateway
            switch ($validatedData['payment_method']) {
                case 'paypal':
                    return $this->initiatePayPalPayment($validatedData['total_price']);
                case 'stripe':
                    return $this->initiateStripePayment($validatedData['total_price']);
                case 'cash':
                    // For cash payments, process immediately
                    $result = $this->executeTransaction($validatedData);
                    $this->sendEmail($result['customer']->email, $result['customerId']);
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Service details saved successfully with cash payment option',
                        'data' => [
                            'service_detail' => $result['serviceDetail'],
                            'order' => $result['order'],
                            'customer' => $result['customer']
                        ]
                    ], 201);
                default:
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid payment method'
                    ], 400);
            }
        } catch (Exception $e) {
            Log::error('Payment process failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save service details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Initiate PayPal payment
     */
    private function initiatePayPalPayment($amount)
    {
        try {
            Log::info('Initiating PayPal payment');
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $returnUrl = route('paypal.success');
            $cancelUrl = route('paypal.cancel');

            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => $returnUrl,
                    "cancel_url" => $cancelUrl,
                ],
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => (float)$amount
                        ]
                    ]
                ]
            ]);

            if (isset($response['id']) && $response['id'] != null) {
                // Success, redirect to PayPal
                foreach ($response['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        session(['paypal_order_id' => $response['id']]);
                        return response()->json([
                            'status' => 'redirect',
                            'redirect_url' => $link['href']
                        ]);
                    }
                }
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create PayPal payment'
            ], 500);
        } catch (Exception $e) {
            Log::error('PayPal payment initialization failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to initialize PayPal payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Initiate Stripe payment
     */
    private function initiateStripePayment($amount)
    {
        try {
            Stripe::setApiKey(config('stripe.secret'));

            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => 'Service Order',
                            ],
                            'unit_amount' => (int)($amount * 100), // Stripe expects amount in cents
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('stripe.cancel'),
            ]);

            return response()->json([
                'status' => 'redirect',
                'redirect_url' => $session->url
            ]);
        } catch (Exception $e) {
            Log::error('Stripe payment initialization failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to initialize Stripe payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process successful PayPal payment
     */
    public function processPayPalSuccess(Request $request)
    {
        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();
            
            $response = $provider->capturePaymentOrder($request->token);
            
            if (isset($response['status']) && $response['status'] == 'COMPLETED') {
                // Get order data from session
                $orderData = session('order_data');
                
                if (!$orderData) {
                    return redirect()->route('payment.error')->with('error', 'Order data not found');
                }
                
                // Execute transaction and save order
                $result = $this->executeTransaction($orderData);
                
                // Save payment details
                $payment = new Payment();
                $payment->order_id = $result['order']->order_id;
                $payment->payment_method = 'paypal';
                $payment->transaction_id = $response['id'];
                $payment->amount = $orderData['total_price'];
                $payment->status = 'completed';
                $payment->save();
                
                // Send confirmation email
                $this->sendEmail($result['customer']->email, $result['customerId']);
                
                // Clear session data
                session()->forget(['order_data', 'paypal_order_id']);
                
                return redirect()->route('payment.success')->with('success', 'Payment completed successfully');
            } else {
                return redirect()->route('payment.cancel')->with('error', 'Payment was not successful');
            }
        } catch (Exception $e) {
            Log::error('PayPal payment processing failed: ' . $e->getMessage());
            return redirect()->route('payment.error')->with('error', 'Error processing payment: ' . $e->getMessage());
        }
    }

    /**
     * Process successful Stripe payment
     */
    public function processStripeSuccess(Request $request)
    {
        try {
            Stripe::setApiKey(config('stripe.secret'));
            $sessionId = $request->get('session_id');
            
            $session = StripeSession::retrieve($sessionId);
            
            if ($session->payment_status == 'paid') {
                // Get order data from session
                $orderData = session('order_data');
                
                if (!$orderData) {
                    return redirect()->route('payment.error')->with('error', 'Order data not found');
                }
                
                // Execute transaction and save order
                $result = $this->executeTransaction($orderData);
                
                // Save payment details
                $payment = new Payment();
                $payment->order_id = $result['order']->order_id;
                $payment->payment_method = 'stripe';
                $payment->transaction_id = $session->payment_intent;
                $payment->amount = $orderData['total_price'];
                $payment->status = 'completed';
                $payment->save();
                
                // Send confirmation email
                $this->sendEmail($result['customer']->email, $result['customerId']);
                
                // Clear session data
                session()->forget('order_data');
                
                return redirect()->route('payment.success')->with('success', 'Payment completed successfully');
            } else {
                return redirect()->route('payment.cancel')->with('error', 'Payment was not successful');
            }
        } catch (Exception $e) {
            Log::error('Stripe payment processing failed: ' . $e->getMessage());
            return redirect()->route('payment.error')->with('error', 'Error processing payment: ' . $e->getMessage());
        }
    }

    /**
     * Handle payment cancellation
     */
    public function handlePaymentCancel()
    {
        // Clear session data
        session()->forget(['order_data', 'paypal_order_id']);
        
        return redirect()->route('payment.cancel')->with('error', 'Payment was cancelled');
    }

   
    private function executeTransaction(array $validatedData)
    {
        try {
            DB::beginTransaction();

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
                'price' => ($validatedData['total_price']),
                'status' => 'active' // Changed from 'inactive' to 'active' since payment is already confirmed
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
                'request_gender' => $validatedData['person_type'] ?? null,
                'request_language' => $validatedData['language'] ?? 'en',
                'business_property' => $validatedData['business_property'] ?? null,
                'cleaning_solvents' => $validatedData['cleaning_solvents'] ?? null,
                'Equipment' => $validatedData['Equipment'] ?? null,
                'status' => 'confirmed' // Changed from 'pending' to 'confirmed' since payment is successful
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

            // Commit transaction
            DB::commit();

            return [
                'customer' => $customer,
                'customerId' => $customerId,
                'order' => $order,
                'serviceDetail' => $serviceDetail
            ];

        } catch (Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            throw $e; // Re-throw the exception to be caught in the save method
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

            // Send email with QR code as an attachment
            \Mail::to($email)->send(new \App\Mail\ServiceOrderConfirmation($data));

            Log::info("Email sent successfully to customer: {$email}");

            return true;
        } catch (Exception $e) {
            Log::error("Failed to send email to customer: {$email}. Error: " . $e->getMessage());
            return false;
        }
    }
}