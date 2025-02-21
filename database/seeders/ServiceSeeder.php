<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                "name" => "Regular Basic Cleaning",
                "price" => "27$",
                "status" => "active"
            ],
            [
                "name" => "One Time Cleaning",
                "price" => "27$",
                "status" => "active"
            ],
            [
                "name" => "Last Minute Cleaning",
                "price" => "57$",
                "status" => "active"
            ],
            [
                "name" => "Deep Cleaning",
                "price" => "59$",
                "status" => "active"
            ],
            [
                "name" => "Move In/Out Cleaning",
                "price" => "59$",
                "status" => "active"
            ],
            [
                "name" => "Post Construction & Renovation Cleaning",
                "price" => "59$",
                "status" => "active"
            ],
            [
                "name" => "Airbnb And Short Term Rental Cleaning",
                "price" => "59$",
                "status" => "active"
            ],
            [
                "name" => "Child Care Services",
                "price" => "59$",
                "status" => "active"
            ],
            [
                "name" => "Elder’s Care Service",
                "price" => "59$",
                "status" => "active"
            ],
            [
                "name" => "Sanitization & Disinfection",
                "price" => "0$",
                "status" => "active"
            ],
            [
                "name" => "Commercial and Office Cleaning",
                "price" => "0$",
                "status" => "active"
            ],
            [
                "name" => "Carpet & Upholstery Cleaning",
                "price" => "0$",
                "status" => "active"
            ],
            [
                "name" => "Move In and Out Transport Cleaning",
                "price" => "0$",
                "status" => "active"
            ],
            [
                "name" => "Steam Cleaning",
                "price" => "0$",
                "status" => "active"
            ],
            [
                "name" => "Pressure Washing",
                "price" => "0$",
                "status" => "active"
            ],
            [
                "name" => "Special Event Cleaning",
                "price" => "0$",
                "status" => "active"
            ],
            [
                "name" => "Pool cleaning",
                "price" => "0$",
                "status" => "active"
            ],
            [
                "name" => "Laundry Services",
                "price" => "0$",
                "status" => "active"
            ]
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

    }
}
