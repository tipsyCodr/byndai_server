<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MasterUserDetail;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@byndai.in'],
            [
                'user_name' => 'Admin',
                'user_mobile' => '9999999999',
                'password' => 'admin123', // Model cast will hash this
                'status' => 'active',
            ]
        );

        // Assign Admin Role
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->assignRole($adminRole);

        // Add Administrator details in MasterUserDetail if not exists
        MasterUserDetail::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'fname' => 'Administrator',
                'mobile_one' => '9999999999',
                'email_one' => 'admin@byndai.in',
                'desg' => 'System Administrator',
            ]
        );
    }
}
