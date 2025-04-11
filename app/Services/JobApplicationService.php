<?php

namespace App\Services;

use App\Http\Controllers\TranslationController;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JobApplicationService
{
    public function save(Request $request)
    {
        // Save the job application
        $jobApplication = new JobApplication();
        $jobApplication->fill($request->all());
        $jobApplication->save();
        
        // Send the PDF to the email if there is one in the request
        if ($request->hasFile('pdf') && $request->file('pdf')->isValid()) {
            $pdf = $request->file('pdf');
            
            $translatedData = TranslationController::translateJson($jobApplication->toArray(), 'en');
    
            Log::info('Job Application Data:', ['email' => $translatedData['email'] ?? null]);
    
            // Send email with PDF attachment
            \Mail::send('emails.job_application', ['data' => $translatedData], function ($message) use ($pdf, $translatedData) {
                $message->to('nipuna315np@gmail.com', 'PearlySky PLC')
                        ->from($translatedData['email'] ?? 'no-reply@pearlysky.com', $translatedData['first_name'] . ' ' . $translatedData['last_name'])
                        ->subject('New Job Application - ' . $translatedData['id'])
                        ->attach($pdf->getRealPath(), [
                            'as' => $translatedData['id'] . '-' . $translatedData['first_name'] . '-' . $translatedData['last_name'] . '.pdf',
                            'mime' => 'application/pdf',
                        ]);
            });
        }
        return $jobApplication;
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
