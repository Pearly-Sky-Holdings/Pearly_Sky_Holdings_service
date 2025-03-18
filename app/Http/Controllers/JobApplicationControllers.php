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
    $this->jobApplicationService->save($request);
    $request = DB::table('customers')
      ->Where('email', 'LIKE', '%' . $request->email . '%')
      ->get();
    return response()->json($request, 201);
  }
}
