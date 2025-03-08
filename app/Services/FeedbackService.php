<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackService
{
    public function save(Request $request)
    {
        $feedback = new Feedback();
        $feedback->fill($request->all());
        return $feedback->save();
    }
}
