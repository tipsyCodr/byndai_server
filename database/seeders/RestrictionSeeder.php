<?php

namespace Database\Seeders;

use App\Models\Restriction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RestrictionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restriction = [
            ['title' => 'Images', 'slug' => 'images', 'plan_id' => 1, 'status' => 1],
            ['title' => 'Video', 'slug' => 'video', 'plan_id' => 1, 'status' => 1],
            ['title' => 'Profile Banner', 'slug' => 'profile_banner', 'plan_id' => 1, 'status' => 1],
        ];
        Restriction::insert($restriction);
    }
}
