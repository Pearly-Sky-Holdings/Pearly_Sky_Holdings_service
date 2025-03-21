<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ServiceDetails;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;


class CustomerControllers extends Controller
{

  protected CustomerService $customerService;


  public function __construct(CustomerService $customerService)
  {
    $this->customerService = $customerService;
  }


  public function getAll()
  {
    $customer = DB::table('customers')
      ->get();
    return response()->json($customer, 200);
  }


  public function search(string $input): JsonResponse
  {
    $customers = DB::table('customers')
      ->where('customer_id', $input)
      ->get();

    $result = [];

    foreach ($customers as $customer) {
      // Get service details for this customer
      $serviceDetails = ServiceDetails::with('service', 'ItemDetails', 'packageDetails')->where('customer_id', $customer->customer_id)
        ->get();

      // Get order details for this customer
      $orderDetails = DB::table('orders')
        ->where('customer_id', $customer->customer_id)
        ->get();

      // Add customer details along with their service and order details
      $result[] = [
        'customer' => $customer,
        'serviceDetails' => $serviceDetails,
        'orderDetails' => $orderDetails
      ];
    }

    return response()->json($result, 200);
  }


  public function save(Request $request)
  {
    $this->customerService->save($request);
    $request = DB::table('customers')
      ->Where('email', 'LIKE', '%' . $request->email . '%')
      ->get();
    return response()->json($request, 201);
  }


  public function update(Request $request, $id)
  {
    $result = $this->customerService->update($request, $id);
    return response()->json($result, 201);
  }


  public function delete(int $id)
  {
    $customer = Customer::find($id);
    $customer->delete();
    return response()->json($customer, 201);
  }
}