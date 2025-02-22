<?php

namespace Database\Seeders;

use App\Models\ReStockingChecklist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReStockingChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $checklists = [
            ["name" => "Hand Soap", "category" => "Bathrooms"],
            ["name" => "Small Trash Bag", "category" => "Bathrooms"],
            ["name" => "Shampoo,Conditioner,Body Wash", "category" => "Bathrooms"],
            ["name" => "4 Large Towels", "category" => "Bathrooms"],
            ["name" => "2 Wash Rags, 1 Black Makeup Cloth", "category"=> "Bathrooms"],
            ["name" => "1 Floor Bath Mat", "category" => "Bathrooms"],
            ["name" => "Makeup Remover Pack, Toothpaste, Toothpaste , Toothbrush", "category" => "Bathrooms"],
            ["name" => "Toilet Paper", "category" => "Bathrooms"],
            
            ["name" => "Coffee Pods", "category" => "Kitchen"],
            ["name" => "Sugar", "category" => "Kitchen"],
            ["name" => "Creamer", "category" => "Kitchen"],
            ["name" => "Dish Soap & Dishwasher Pods", "category" => "Kitchen"],
            ["name" => "Trash Bags", "category" => "Kitchen"],
            ["name" => "Paper Towels", "category" => "Kitchen"],
            ["name" => "Dish Brush / Sponge", "category" => "Kitchen"],
            
            ["name" => "4 Pillowcases Per Bed", "category" => "BedRooms"],
            ["name" => "Bottom Sheet", "category" => "BedRooms"],
            ["name" => "Top Sheet", "category" => "BedRooms"],
            ["name" => "Duvet Cover & Shams", "category" => "BedRooms"],
            
            ["name" => "Wall Cleaning", "category" => "Miscellaneous"],
            ["name" => "Water Bottle", "category" => "Miscellaneous"],
        ];

        foreach ($checklists as $checklist) {
            ReStockingChecklist::create($checklist);
        }

    }
}
