<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;

class AuthControllers extends Controller
{
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:4'
        ]);

        $employee = Employee::where('email', $fields['email'])->first();
        if (!$employee) {
            $customer = Customer::where('email', $fields['email'])->first();
            if (!$customer || !Hash::check($fields['password'], $customer->password)) {
                return response(['message' => 'Invalid email or password'], 401);
            }
            $token = $customer->createToken('token')->plainTextToken;
            $response = [
                'type' => 'customer',
                'user' => $customer,
                'token' => $token
            ];


        } else {
            if (!$employee || !Hash::check($fields['password'], $employee->password)) {
                return response(['message' => 'Invalid email or password'], 401);
            }
            $token = $employee->createToken('token')->plainTextToken;
            $response = [
                'type' => 'employee',
                'user' => $employee,
                'token' => $token
            ];
        }
        return response($response, 201);
    }
}
