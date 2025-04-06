<?php

namespace App\Http\Controllers;
use \App\Models\CustomerMassage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerMassageControllers extends Controller
{

    // save customer massages
    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'contact' => 'required',
            'massage' => 'required',
        ]);

        $customerMessage = CustomerMassage::create($request->all());
    
        $data = TranslationController::translateJson($customerMessage->toArray(), 'en');
   
        // Send email
        \Mail::send('emails.customer_massage', ['data' => $data], function ($message) use ($data) {
            $message->to('nipuna315np@gmail.com', 'PearlySky PLC')
                     ->from($data['email'], 'PearlySky PLC')
                    ->subject('Customer massage - ' . $data['id']);
        });

        return redirect()->back()->with('success',201);
    }
}
