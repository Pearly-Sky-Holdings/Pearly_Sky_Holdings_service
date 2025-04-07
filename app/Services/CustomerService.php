<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerService
{
    public function save(Request $request)
    {
        $customer = new Customer();
        $customer->fill($request->all());
        $customer->password = Hash::make($customer->password);
        return $customer->save();
    }


    public function update(Request $request, $id)
    {
        // Update logic here
        $customer = Customer::find($id);
        if ($customer) {
            $customer->update($request->all());
            return $customer;
        }
        return null;
    }
}
