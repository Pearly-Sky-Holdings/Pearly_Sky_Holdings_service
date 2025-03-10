<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::create([
            'first_name' => 'Kasun',
            'last_name' => 'Perera',
            'company' => 'ABC Company',
            'country' => 'Sri Lanka',
            'city' => 'Colombo',
            'province' => 'Western',
            'postal_code' => '10100',
            'contact' => '0771234567',
            'email' => 'nipuna315np@gmail.com',
            'password' => Hash::make('123456')
        ]);
    }
}
