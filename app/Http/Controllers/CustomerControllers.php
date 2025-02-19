<?php

namespace App\Http\Controllers;

use App\Models\Customer;
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
      return response()->json($customer,200);
    }


    public function search(string $input): JsonResponse
  {
    $company = DB::table('customers')
      ->where('customer_id', 'LIKE', '%' . $input . '%')
      ->orWhere('first_name', 'LIKE', '%' . $input . '%')
      ->orWhere('last_name', 'LIKE', '%' . $input . '%')
      ->orWhere('email', 'LIKE', '%' . $input . '%')
      ->get();
    return response()->json($company, 201);
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