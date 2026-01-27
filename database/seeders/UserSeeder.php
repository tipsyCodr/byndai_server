<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['user_mobile' => 'Tinku', 'email' => 'tinku@gmail.com', 'password' => bcrypt('1234'), 'status' => 'active',],
            ['user_mobile' => 'Kumar', 'email' => 'kumar@gmail.com', 'password' => bcrypt('1234'), 'status' => 'active',],
            ['user_mobile' => 'Shah', 'email' => 'shah@gmail.com', 'password' => bcrypt('1234'), 'status' => 'active',],
        ];
        User::insert($users);
    }
}
