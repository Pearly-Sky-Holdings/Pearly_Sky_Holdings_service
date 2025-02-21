<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CustomerSeeder::class,
            EmployeeSeeder::class,
            PackageSeeder::class,
            ServiceSeeder::class,
            ServiceWithPackageSeeder::class,
        ]);
    }
}
