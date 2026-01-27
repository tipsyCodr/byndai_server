<?php

namespace Database\Seeders;

use App\Models\MasterCountry;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterCountry::create([
            'ctry_name' => 'India',
        ]);
    }
}
