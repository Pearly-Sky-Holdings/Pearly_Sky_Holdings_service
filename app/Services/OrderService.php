<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderService
{
    public function save(Request $request)
    {
        $order  = new Order();
        $order ->fill($request->all());
        return $order ->save();
    }


    public function update(Request $request, $id)
    {
        // Update logic here
        $order  = Order::find($id);
        if ($order ) {
            $order ->update($request->all());
            return $request ;
        }
        return null;
    }
}
