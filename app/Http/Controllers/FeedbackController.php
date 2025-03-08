<?php

namespace App\Http\Controllers;
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
  

  public function getAll()
  {
    $customer = DB::table('feedback')->get();
    return response()->json($customer,200);
  }
}
