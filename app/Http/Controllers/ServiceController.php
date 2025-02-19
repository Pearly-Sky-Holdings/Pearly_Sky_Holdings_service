<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Services\ServiceService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{

    protected ServiceService $serviceService;


    public function __construct(ServiceService $serviceService)
    {
      $this->serviceService = $serviceService;
    }


    public function getAll()
    {
        $services = Service::all();
        return response()->json($services,200);
    }


    public function save(Request $request)
    {
      $this->serviceService->save($request);
      return response()->json($request, 201);
  }


  public function update(Request $request, $id)
  {
    $result = $this->serviceService->update($request, $id);
    return response()->json($result, 201);
  }

  // Get a specific service
  public function search($id)
  {
      $service = Service::find($id);
      
      if (!$service) {
          return response()->json([
              'status' => 'error',
              'message' => 'Service not found'
          ], 404);
      }
      
      return response()->json($service,200);
  }


  // Delete a service
  public function destroy($id)
  {
      $service = Service::find($id);
      
      if (!$service) {
          return response()->json([
              'status' => 'error',
              'message' => 'Service not found'
          ], 404);
      }
      
      $service->delete();
      
      return response()->json([
          'status' => 'success',
          'message' => 'Service deleted successfully'
      ]);
  }

}
