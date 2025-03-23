<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ServiceOrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
        
    }

    public function build()
    {
        return $this->from('Systempearlyskycleaningplc@gmail.com', 'PearlySky PLC')
            ->subject('Service Order Confirmation')
            ->view('emails.service-order-confirmation')
            ->with(['data' => $this->data]);
    }
}