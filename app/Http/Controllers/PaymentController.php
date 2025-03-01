<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ServiceDetailsService;

class PaymentController extends Controller
{
    protected $serviceDetailsService;

    public function __construct(ServiceDetailsService $serviceDetailsService)
    {
        $this->serviceDetailsService = $serviceDetailsService;
    }

    public function paypalSuccess(Request $request)
    {
        return $this->serviceDetailsService->processPayPalSuccess($request);
    }

    public function paypalCancel()
    {
        return $this->serviceDetailsService->handlePaymentCancel();
    }

    public function stripeSuccess(Request $request)
    {
        return $this->serviceDetailsService->processStripeSuccess($request);
    }

    public function stripeCancel()
    {
        return $this->serviceDetailsService->handlePaymentCancel();
    }

    public function showSuccess()
    {
        return view('payments.success');
    }

    public function showCancel()
    {
        return view('payments.cancel');
    }

    public function showError()
    {
        return view('payments.error');
    }
}