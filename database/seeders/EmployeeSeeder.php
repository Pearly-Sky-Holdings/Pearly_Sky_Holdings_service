<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Employee::create([
            'name' => 'John Admin',
            'age' => 30,
            'address' => '123 Main Street, Colombo',
            'position' => 'Administrator',
            'contact' => '0771234567',
            'email' => 'admin@example.com',
            'status' => 'active',
            'password' => Hash::make('1234')
        ]);
    }
}
