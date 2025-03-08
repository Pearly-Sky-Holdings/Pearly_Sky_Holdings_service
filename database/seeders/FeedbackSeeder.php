<?php

namespace Database\Seeders;

use App\Models\Feedback;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
               'star_count' => '5',
                'name' => "Viktoriya Fromm",
                'description' => Str::limit("I booked a cleaning Service in september 2024. Two Cleaners did a good job, for a 5 hours work, they also cleaned the windows and fridge, were very punctual, friendly and reliable. ", 255),
                'date' =>  Carbon::now()->toDateString(),
                "social_media_type"=>"google"
            ],
            [
                'star_count' => '4',
                 'name' => "Viktoriya Fromm",
                 'description' => Str::limit("I booked a cleaning Service in september 2024. Two Cleaners did a good job, for a 5 hours work, they also cleaned the windows and fridge, were very punctual, friendly and reliable.", 255),
                 'date' =>  Carbon::now()->toDateString(),
                 "social_media_type"=>"facebook"
            ],
            [
                'star_count' => '4',
                 'name' => "Viktoriya Fromm",
                 'description' => Str::limit("I booked a cleaning Service in september 2024.", 255),
                 'date' =>  Carbon::now()->toDateString(),
                 "social_media_type"=>"yelp"
            ],
            [
                'star_count' => '4',
                'name' => "Viktoriya Fromm",
                'description' => Str::limit("I booked a cleaning Service in september 2024. Two Cleaners did a good job, for a 5 hours work, they also cleaned the windows and fridge, were very punctual, friendly and reliable.", 255),
                'date' =>  Carbon::now()->toDateString(),
                "social_media_type"=>"trustpilot"
             ]
           
        ];

        foreach ($items as $itemData) {
            Feedback::create($itemData);
        }
    }
}
