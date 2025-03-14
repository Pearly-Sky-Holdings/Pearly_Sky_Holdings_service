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
            ["name" => "Hand Soap", "category" => "Bathrooms", "type"=>"all"],
            ["name" => "Small Trash Bag", "category" => "Bathrooms", "type"=>"all"],
            ["name" => "Shampoo,Conditioner,Body Wash", "category" => "Bathrooms", "type"=>"all"],
            ["name" => "4 Large Towels", "category" => "Bathrooms", "type"=>"all"],
            ["name" => "2 Wash Rags, 1 Black Makeup Cloth", "category"=> "Bathrooms", "type"=>"all"],
            ["name" => "1 Floor Bath Mat", "category" => "Bathrooms", "type"=>"all"],
            ["name" => "Makeup Remover Pack, Toothpaste, Toothpaste , Toothbrush", "category" => "Bathrooms", "type"=>"all"],
            ["name" => "Toilet Paper", "category" => "Bathrooms", "type"=>"all"],
            
            ["name" => "Coffee Pods", "category" => "Kitchen", "type"=>"all"],
            ["name" => "Sugar", "category" => "Kitchen", "type"=>"all"],
            ["name" => "Creamer", "category" => "Kitchen", "type"=>"all"],
            ["name" => "Dish Soap & Dishwasher Pods", "category" => "Kitchen", "type"=>"all"],
            ["name" => "Trash Bags", "category" => "Kitchen", "type"=>"all"],
            ["name" => "Paper Towels", "category" => "Kitchen", "type"=>"all"],
            ["name" => "Dish Brush / Sponge", "category" => "Kitchen", "type"=>"all"],
            ["name" => "Cutting board", "category" => "Kitchen", "type"=>"all"],
            
            ["name" => "4 Pillowcases Per Bed", "category" => "BedRooms", "type"=>"all"],
            ["name" => "Bottom Sheet", "category" => "BedRooms", "type"=>"all"],
            ["name" => "Top Sheet", "category" => "BedRooms", "type"=>"all"],
            ["name" => "Duvet Cover & Shams", "category" => "BedRooms", "type"=>"all"],
            ["name" => "Mattress", "category" => "BedRooms", "type"=>"all"],
            ["name" => "Hangers", "category" => "BedRooms", "type"=>"all"],
            
            ["name" => "Wall Cleaning", "category" => "Miscellaneous", "type"=>"all"],
            ["name" => "Water Bottle", "category" => "Miscellaneous", "type"=>"all"],

            ["name" => "TV Stand", "category" => "Living_Room", "type"=>"some"],
            ["name" => "Coffee Table", "category" => "Living_Room", "type"=>"some"],
            ["name" => "Couch/Chair", "category" => "Living_Room", "type"=>"some"],
            ["name" => "Curtains/Shades", "category" => "Living_Room", "type"=>"some"],
            ["name" => "Streaming device", "category" => "Living_Room", "type"=>"some"],

            ["name" => "Oven", "category" => "House_Appliances", "type"=>"some"],
            ["name" => "Fridge", "category" => "House_Appliances", "type"=>"some"],
            ["name" => "Iron", "category" => "House_Appliances", "type"=>"some"],
            ["name" => "Washing Machine", "category" => "House_Appliances", "type"=>"some"],
            ["name" => "Vacuum Cleaner", "category" => "House_Appliances", "type"=>"some"]

        ];

        foreach ($checklists as $checklist) {
            ReStockingChecklist::create($checklist);
        }

    }
}
