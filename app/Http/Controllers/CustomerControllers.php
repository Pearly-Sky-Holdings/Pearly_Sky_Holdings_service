<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CustomerControllers extends Controller
{

    protected CustomerService $customerService;


    public function __construct(CustomerService $customerService)
    {
      $this->customerService = $customerService;
    }


    public function index()
    {
      return Customer::with('contactperson')->get();
    }


//     public function search(string $input): JsonResponse
//   {
//     $company = Customer::with('contactperson')
//       ->where('cust_id', 'LIKE', '%' . $input . '%')
//       ->orWhere('cust_name', 'LIKE', '%' . $input . '%')
//       ->orWhere('cust_contact', 'LIKE', '%' . $input . '%')
//       ->get();
//     return response()->json($company, 201);
//   }


  public function save(Request $request)
  {
    return $this->customerService->save($request);
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