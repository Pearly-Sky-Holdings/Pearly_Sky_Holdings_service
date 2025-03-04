<?php

namespace App\Http\Controllers;

use App\Services\ServiceDetailsService;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;

class PaymentController extends Controller
{
    protected $serviceDetailsService;

    public function __construct(ServiceDetailsService $serviceDetailsService)
    {
        $this->serviceDetailsService = $serviceDetailsService;
    }

    /**
     * Process a payment for an existing order
     */
    public function processPayment(Request $request)
    {
        // Validation
        $request->validate([
            'order_id' => 'required',
            'payment_method' => 'required|in:paypal,stripe',
        ]);
        
        // Retrieve order
        $order = Order::findOrFail($request->order_id);
        
        // Check if payment is already processed
        $existingPayment = Payment::where('order_id', $order->order_id)
            ->where('status', 'completed')
            ->first();
            
        if ($existingPayment) {
            return response()->json([
                'status' => 'error',
                'message' => 'This order has already been paid for'
            ], 400);
        }
        
        // Call appropriate payment method
        if ($request->payment_method === 'paypal') {
            // Logic to initiate PayPal payment
            // This could be implemented in the ServiceDetailsService
        } else {
            // Logic to initiate Stripe payment
        }
    }
    
    /**
     * Handle successful payment callback
     */
    public function handleSuccess(Request $request, $orderId, $method)
    {
        return $this->serviceDetailsService->handlePaymentSuccess($request, $orderId, $method);
    }
    
    /**
     * Handle cancelled payment
     */
    public function handleCancel($orderId)
    {
        return $this->serviceDetailsService->handlePaymentCancel($orderId);
    }
    
    /**
     * Get payment status for an order
     */
    public function getPaymentStatus($orderId)
    {
        $payment = Payment::where('order_id', $orderId)->first();
        
        if (!$payment) {
            return response()->json([
                'status' => 'error',
                'message' => 'No payment found for this order'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'payment_status' => $payment->status,
                'payment_method' => $payment->payment_method,
                'amount' => $payment->amount,
                'transaction_id' => $payment->transaction_id,
                'created_at' => $payment->created_at,
                'updated_at' => $payment->updated_at
            ]
        ]);
    }
}