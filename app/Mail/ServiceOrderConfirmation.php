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
    public $qrCodePath;

    public function __construct($data, $qrCodePath = null)
    {
        $this->data = $data;
        $this->qrCodePath = $qrCodePath;
        
    }

    public function build()
    {
        $email = $this->from('Systempearlyskycleaningplc@gmail.com', 'PearlySky PLC')
            ->subject('Service Order Confirmation')
            ->view('emails.service-order-confirmation')
            ->with(['data' => $this->data]);

            if ($this->qrCodePath) {
                $email->attach($this->qrCodePath, [
                    'as' => 'qr_code.png',
                    'mime' => 'image/png',
                ]);
            }
            return $email;
    }
}