<?php

namespace App\Services;

use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobApplicationService
{
    public function save(Request $request)
    {
        $data = new JobApplication();
        $data->fill($request->all());
        return $data->save();
    }


    public function update(Request $request, $id)
    {
        // Update logic here
        $data = JobApplication::find($id);
        if ($data) {
            $data->update($request->all());
            return $data;
        }
        return null;
    }
}
