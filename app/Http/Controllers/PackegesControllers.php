<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PackegesControllers extends Controller
{
    public function getDevices()
    {
        Log::info('Get all packages');
      $packagesDetails = DB::table('packages')
        ->get();
      return response()->json($packagesDetails,200);
    }
}
