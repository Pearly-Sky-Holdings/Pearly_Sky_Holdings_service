<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServiceService
{
    public function save(Request $request)
    {
        $service = new Service();
        $service->fill($request->all());
        Log::info('Service saved', $service->toArray());
        return $service->save();
    }


    public function update(Request $request, $id)
    {
        // Update logic here
        $service = Service::find($id);
        if ($service) {
            $service->update($request->all());
            return $service;
        }
        return null;
    }
}
