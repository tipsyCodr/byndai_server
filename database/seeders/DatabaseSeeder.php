<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            StateSeeder::class,
            DistrictSeeder::class,
            MasterIndustrySeeder::class,
            MasterLinkSeeder::class,
            UserSeeder::class,
            ProductSeerder::class,
            ProductImageSeeder::class,
            PlanSeeder::class,
            RestrictionSeeder::class,
            SubscriptionSeeder::class,
        ]);
    }
}
