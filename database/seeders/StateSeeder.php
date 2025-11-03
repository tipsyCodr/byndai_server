<?php

namespace Database\Seeders;

use App\Models\MasterCountry;
use App\Models\MasterState;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $india = MasterCountry::where('ctry_name', 'India')->first();
        if (!$india) {
            $this->command->info('India not found in countries table. Please run CountrySeeder first.');
            return;
        }
        $states = ['Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh', 'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand', 'Karnataka', 'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur', 'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab', 'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura', 'Uttar Pradesh', 'Uttarakhand', 'West Bengal', 'Delhi', 'Jammu and Kashmir', 'Ladakh', 'Puducherry'];
        foreach ($states as $state) {
            MasterState::create(['state_name' => $state, 'c_id' => $india->id]);
        }
        $this->command->info('India states seeded successfully!');
    }
}
