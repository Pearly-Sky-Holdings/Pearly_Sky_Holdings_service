<?php

namespace App\Http\Controllers;

use App\Models\ServicePackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackegesControllers extends Controller
{
  public function getDevices()
  {
    $packagesDetails = DB::table('packages')
      ->get();
    return response()->json($packagesDetails, 200);
  }


  public function getPackagesByService($serviceId, Request $request)
  {
    $country = $request->query('country','EN');
    $service = ServicePackage::with('package')->where('service_id', $serviceId)
      ->get();

    if (!$service) {
      return response()->json(['message' => 'Service not found'], 404);
    }

    $packages = $service->pluck('package');
    $translatedData = TranslationController::translateJson($packages->toArray(), $country);


      return response()->json($translatedData, 200);
  }
}
