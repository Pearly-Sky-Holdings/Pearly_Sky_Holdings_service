<?php

namespace Database\Seeders;

use App\Models\ServiceDetails;
use App\Models\ServicePackage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceWithPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            ["package_id" => "1", "service_id" => "1"],
            ["package_id" => "2", "service_id" => "1"],
            ["package_id" => "3", "service_id" => "1"],
            ["package_id" => "4", "service_id" => "1"],
            ["package_id" => "5", "service_id" => "1"],
            ["package_id" => "6", "service_id" => "1"],
            ["package_id" => "7", "service_id" => "1"],
            ["package_id" => "8", "service_id" => "1"],
            ["package_id" => "9", "service_id" => "1"],
            ["package_id" => "10", "service_id" => "1"],
            ["package_id" => "11", "service_id" => "1"],
            ["package_id" => "12", "service_id" => "1"],
            ["package_id" => "13", "service_id" => "1"],
            ["package_id" => "14", "service_id" => "1"],
            ["package_id" => "15", "service_id" => "1"],
            ["package_id" => "16", "service_id" => "1"],
            ["package_id" => "17", "service_id" => "1"],
            ["package_id" => "18", "service_id" => "1"],
            ["package_id" => "19", "service_id" => "1"],
            ["package_id" => "20", "service_id" => "1"],
            ["package_id" => "21", "service_id" => "1"],
            ["package_id" => "22", "service_id" => "1"],
            

            ["package_id" => "1", "service_id" => "2"],
            ["package_id" => "23", "service_id" => "2"],
            ["package_id" => "3", "service_id" => "2"],
            ["package_id" => "4", "service_id" => "2"],
            ["package_id" => "5", "service_id" => "2"],
            ["package_id" => "6", "service_id" => "2"],
            ["package_id" => "7", "service_id" => "2"],
            ["package_id" => "8", "service_id" => "2"],
            ["package_id" => "9", "service_id" => "2"],
            ["package_id" => "10", "service_id" => "2"],
            ["package_id" => "11", "service_id" => "2"],
            ["package_id" => "12", "service_id" => "2"],
            ["package_id" => "13", "service_id" => "2"],
            ["package_id" => "14", "service_id" => "2"],
            ["package_id" => "15", "service_id" => "2"],
            ["package_id" => "16", "service_id" => "2"],
            ["package_id" => "17", "service_id" => "2"],
            ["package_id" => "18", "service_id" => "2"],
            ["package_id" => "19", "service_id" => "2"],
            ["package_id" => "20", "service_id" => "2"],
            ["package_id" => "21", "service_id" => "2"],
            ["package_id" => "22", "service_id" => "2"],

            ["package_id" => "1", "service_id" => "3"],
            ["package_id" => "23", "service_id" => "4"],
            ["package_id" => "3", "service_id" => "3"],
            ["package_id" => "4", "service_id" => "3"],
            ["package_id" => "5", "service_id" => "3"],
            ["package_id" => "6", "service_id" => "3"],
            ["package_id" => "7", "service_id" => "3"],
            ["package_id" => "8", "service_id" => "3"],
            ["package_id" => "9", "service_id" => "3"],
            ["package_id" => "10", "service_id" => "3"],
            ["package_id" => "11", "service_id" => "3"],
            ["package_id" => "12", "service_id" => "3"],
            ["package_id" => "13", "service_id" => "3"],
            ["package_id" => "14", "service_id" => "3"],
            ["package_id" => "15", "service_id" => "3"],
            ["package_id" => "16", "service_id" => "3"],
            ["package_id" => "17", "service_id" => "3"],
            ["package_id" => "18", "service_id" => "3"],
            ["package_id" => "19", "service_id" => "3"],
            ["package_id" => "20", "service_id" => "3"],
            ["package_id" => "21", "service_id" => "3"],
            ["package_id" => "22", "service_id" => "3"],

            ["package_id" => "1", "service_id" => "4"],
            ["package_id" => "23", "service_id" => "4"],
            ["package_id" => "3", "service_id" => "4"],
            ["package_id" => "4", "service_id" => "4"],
            ["package_id" => "5", "service_id" => "4"],
            ["package_id" => "6", "service_id" => "4"],
            ["package_id" => "7", "service_id" => "4"],
            ["package_id" => "8", "service_id" => "4"],
            ["package_id" => "9", "service_id" => "4"],
            ["package_id" => "10", "service_id" => "4"],
            ["package_id" => "11", "service_id" => "4"],
            ["package_id" => "12", "service_id" => "4"],
            ["package_id" => "13", "service_id" => "4"],
            ["package_id" => "14", "service_id" => "4"],
            ["package_id" => "15", "service_id" => "4"],
            ["package_id" => "16", "service_id" => "4"],
            ["package_id" => "17", "service_id" => "4"],
            ["package_id" => "18", "service_id" => "4"],
            ["package_id" => "19", "service_id" => "4"],
            ["package_id" => "20", "service_id" => "4"],

            ["package_id" => "1", "service_id" => "5"],
            ["package_id" => "23", "service_id" => "5"],
            ["package_id" => "3", "service_id" => "5"],
            ["package_id" => "4", "service_id" => "5"],
            ["package_id" => "5", "service_id" => "5"],
            ["package_id" => "6", "service_id" => "5"],
            ["package_id" => "7", "service_id" => "5"],
            ["package_id" => "8", "service_id" => "5"],
            ["package_id" => "9", "service_id" => "5"],
            ["package_id" => "10", "service_id" => "5"],
            ["package_id" => "11", "service_id" => "5"],
            ["package_id" => "12", "service_id" => "5"],
            ["package_id" => "13", "service_id" => "5"],
            ["package_id" => "14", "service_id" => "5"],
            ["package_id" => "15", "service_id" => "5"],
            ["package_id" => "16", "service_id" => "5"],
            ["package_id" => "17", "service_id" => "5"],
            ["package_id" => "18", "service_id" => "5"],
            ["package_id" => "19", "service_id" => "5"],
            ["package_id" => "20", "service_id" => "5"],
            ["package_id" => "21", "service_id" => "5"],
            ["package_id" => "22", "service_id" => "5"],
        
        ];

        foreach ($services as $service) {
            ServicePackage::create($service);
        }
    }
}
