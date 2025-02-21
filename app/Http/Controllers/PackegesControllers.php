<?php

namespace App\Http\Controllers;

use App\Models\ServicePackage;
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
    return response()->json($packagesDetails, 200);
  }


  public function getPackagesByService($serviceId)
  {
    $service = ServicePackage::with('package')->where('service_id', $serviceId)
      ->get();

    if (!$service) {
      return response()->json(['message' => 'Service not found'], 404);
    }

    $packages = $service->pluck('package');
    return response()->json($packages);
  }
}
