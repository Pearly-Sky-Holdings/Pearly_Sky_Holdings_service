<?php

namespace App\Http\Controllers;

use App\Services\JobApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobApplicationControllers extends Controller
{

    protected JobApplicationService $jobApplicationService;


    public function __construct(JobApplicationService $jobApplicationService)
    {
      $this->jobApplicationService = $jobApplicationService;
    }


    public function save(Request $request)
  {
    $saved = $this->jobApplicationService->save($request);
    return response()->json($saved, 201);
  }
}
