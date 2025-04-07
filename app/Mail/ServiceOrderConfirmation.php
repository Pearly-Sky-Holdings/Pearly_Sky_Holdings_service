<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ServiceOrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    private const SYSTEM_EMAIL = 'Systempearlyskycleaningplc@gmail.com';

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {        
        // Check the language and return the appropriate view
        if ($this->data['language'] == 'en' || $this->data['language'] == 'us') {
            return $this->from(self::SYSTEM_EMAIL, 'PearlySky PLC')
            ->subject('Service Order Confirmation')
            ->view('emails.orderconfotmationMail.service-order-confirmation')
            ->with(['data' => $this->data]);

        }elseif($this->data['language'] == 'jp'){
            return $this->from(self::SYSTEM_EMAIL, 'PearlySky PLC')
            ->subject('Service Order Confirmation')
            ->view('emails.orderconfotmationMail.japan-language')
            ->with(['data' => $this->data]);

        }elseif($this->data['language'] == 'cn'){
            return $this->from(self::SYSTEM_EMAIL, 'PearlySky PLC')
            ->subject('Service Order Confirmation')
            ->view('emails.orderconfotmationMail.chaina-language')
            ->with(['data' => $this->data]);
        }elseif($this->data['language'] == 'fr'){
            return $this->from(self::SYSTEM_EMAIL, 'PearlySky PLC')
            ->subject('Service Order Confirmation')
            ->view('emails.orderconfotmationMail.french-language')
            ->with(['data' => $this->data]);
        }elseif($this->data['language'] == 'nl'){
            return $this->from(self::SYSTEM_EMAIL, 'PearlySky PLC')
            ->subject('Service Order Confirmation')
            ->view('emails.orderconfotmationMail.dutch-language')
            ->with(['data' => $this->data]);
        }elseif($this->data['language'] == 'de'){
            return $this->from(self::SYSTEM_EMAIL, 'PearlySky PLC')
            ->subject('Service Order Confirmation')
            ->view('emails.orderconfotmationMail.german-language')
            ->with(['data' => $this->data]);
        }elseif($this->data['language'] == 'es'){
            return $this->from(self::SYSTEM_EMAIL, 'PearlySky PLC')
            ->subject('Service Order Confirmation')
            ->view('emails.orderconfotmationMail.spanish-language')
            ->with(['data' => $this->data]);
        }elseif($this->data['language'] == 'se'){
            return $this->from(self::SYSTEM_EMAIL, 'PearlySky PLC')
            ->subject('Service Order Confirmation')
            ->view('emails.orderconfotmationMail.swedish-language')
            ->with(['data' => $this->data]);
        }elseif($this->data['language'] == 'ar'){
            return $this->from(self::SYSTEM_EMAIL, 'PearlySky PLC')
            ->subject('Service Order Confirmation')
            ->view('emails.orderconfotmationMail.arabic-language')
            ->with(['data' => $this->data]);
        }elseif($this->data['language'] == 'fi'){
            return $this->from(self::SYSTEM_EMAIL, 'PearlySky PLC')
            ->subject('Service Order Confirmation')
            ->view('emails.orderconfotmationMail.finnish-language')
            ->with(['data' => $this->data]);
        }
            return $this->from(self::SYSTEM_EMAIL, 'PearlySky PLC')
            ->subject('Service Order Confirmation')
            ->view('emails.orderconfotmationMail.service-order-confirmation')
            ->with(['data' => $this->data]);
        
       
    }
}