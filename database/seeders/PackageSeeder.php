<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Packege;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Balcony Cleaning',
                'price' => '0$',
                'status' => 'active'
            ],
            [
                'name' => 'Chemical Cleaning',
                'price' => '0$',
                'status' => 'active'
            ],
            [
                'name' => 'Clean the Kitchen Appliances',
                'price' => '0$',
                'status' => 'active'
            ],
            [
                'name' => 'Cleaning and Disinfect Pet Areas',
                'price' => '0$',
                'status' => 'active'
            ],
            [
                'name' => 'Cleaning Doors',
                'price' => '0$',
                'status' => 'active'
            ],
            [
                'name' => 'Cleaning Outdoor Furniture',
                'price' => '0$',
                'status' => 'active'
            ],
            [
                'name' => 'Dust Cleaning Fan and Light Fixtures',
                'price' => '0$',
                'status' => 'active'
            ],
            [
                'name' => 'Garden Cleaning',
                'price' => '0$',
                'status' => 'active'
            ],
            [
                'name' => 'Ironing',
                'price' => '0$',
                'status' => 'active'
            ],
            [
                'name' => 'Laundry Service',
                'price' => '0$',
                'status' => 'active'
            ],
            [
                'name' => 'Limescale Removal',
                'price' => '0$',
                'status' => 'active'
            ],
            [
                'name' => 'Mold Removal',
                'price' => '0$',
                'status' => 'active'
            ],
            [
                'name' => 'Organizing/Packing',
                'price' => '0$',
                'status' => 'active'
            ],
            [
                'name' => 'Sweep and Tidy Outdoor Areas',
                'price' => '0$',
                'status' => 'active'
            ],
            [
                'name' => 'Vacuum Carpet and Area Rugs',
                'price' => '0$',
                'status' => 'active'
            ],
            [
                'name' => 'Vacuum Carpet and Oregons',
                'price' => '0$',
                'status' => 'active'
            ],
            [
                'name' => 'Vacuum Furniture and Upholstery',
                'price' => '0$',
                'status' => 'active'
            ],
            [
                'name' => 'Wall Cleaning',
                'price' => '0$',
                'status' => 'active'
            ],
            [
                'name' => 'Wash and Change Bed Linens',
                'price' => '0$',
                'status' => 'active'
            ],
            [
                'name' => 'Window Cleaning (House & Apartment)',
                'price' => '0$',
                'status' => 'active'
            ],
            [
                'name' => 'Fridge Cleaning',
                'price' => '75$',
                'status' => 'active'],
            [
                'name' => 'Oven Cleaning',
                'price' => '85$',
                'status' => 'active'
            ],
            [
                'name' => 'Cleaning Windows Inside and Outside',
                'price' => '8$',
                'status' => 'active'
            ]
        ];

        foreach ($packages as $package) {
            Packege::create($package);
        }
    }
}
