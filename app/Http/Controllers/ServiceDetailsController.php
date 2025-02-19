<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ServiceDetailsService;

class ServiceDetailsController extends Controller
{

    protected ServiceDetailsService $serviceDetailsService;


    public function __construct(ServiceDetailsService $serviceDetailsService)
    {
      $this->serviceDetailsService = $serviceDetailsService;
    }

    public function save(Request $request)
    {
        $isSaveData = $this->serviceDetailsService->save($request);
        return response()->json($isSaveData, 201);
    }
}
