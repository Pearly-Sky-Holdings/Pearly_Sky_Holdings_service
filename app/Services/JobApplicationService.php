<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Http\Request;

class JobApplicationService
{
    public function save(Request $request)
    {
        $customer = new Customer();
        $customer->fill($request->all());
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
