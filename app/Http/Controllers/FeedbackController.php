<?php

namespace App\Http\Controllers;
use App\Models\Feedback;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\FeedbackService;

class FeedbackController extends Controller
{

    protected FeedbackService $feedbackService;


    public function __construct(FeedbackService $feedbackService)
    {
      $this->feedbackService = $feedbackService;
    }

    public function save(Request $request)
  {
      $isSave=$this->feedbackService->save($request);
      return response()->json($isSave);
  }
  

  public function getAll(Request $request)
  {
    $country = $request->query('country','EN');
    $feedback = Feedback::all();
    $translatedData = TranslationController::translateJson($feedback->toArray(), $country);
    return response()->json($translatedData,200);
  }
}
