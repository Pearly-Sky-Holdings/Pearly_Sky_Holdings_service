<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'name' => 'Cleaning Solvent (Eco Friendly Chemicals)',
                'price' => "11.52$",
                'qty' => 1,
                'type' => 'Solvents'
            ],
            [
                'name' => 'MOP',
                'price' => "5.24$",
                'qty' => 1,
                'type' => 'Equipment'
            ],
            [
                'name' => 'Other Cleaning Materials',
                'price' => "6.29$",
                'qty' => 1,
                'type' => 'Equipment'
            ],
            [
                'name' => 'Vacuum Cleaner',
                'price' => "8.38$",
                'qty' => 1,
                'type' => 'Equipment'
            ],
           
        ];

        foreach ($items as $itemData) {
            Item::create($itemData);
        }
    }
}
