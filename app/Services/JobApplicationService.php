<?php

namespace App\Services;

use App\Http\Controllers\TranslationController;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobApplicationService
{
    public function save(Request $request)
{
    // Save the job application
    $data = new JobApplication();
    $data->fill($request->all());
    $data->save();
    
    // Send the PDF to the email if there is one in the request
    if ($request->hasFile('pdf') && $request->file('pdf')->isValid()) {
        $pdf = $request->file('pdf');
        
        $data = TranslationController::translateJson($data->toArray(), 'en');

        // Send email with PDF attachment
        \Mail::send('emails.job_application', ['data' => $data], function ($message) use ($pdf, $data) {
            $message->to('recruiting@pearlyskyplc.com', 'PearlySky PLC')
                     ->from($data->email, 'PearlySky PLC')
                    ->subject('New Job Application - ' . $data->id)
                    ->attach($pdf->getRealPath(), [
                        'as' => $data->id . '-' . $data->first_name . '-' . $data->last_name . '.pdf',
                        'mime' => 'application/pdf',
                    ]);
        });
    }
    return $data;
}


    public function update(Request $request, $id)
    {
        // Update logic here
        $data = JobApplication::find($id);
        if ($data) {
            $data->update($request->all());
            return $data;
        }
        return null;
    }
}
