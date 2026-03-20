<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MasterUserDetail;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create OR Update Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@byndai.in'],
            [
                'user_name' => 'admin',
                'user_mobile' => '9999999999',
                'password' => 'admin123', // Model casts automatically hashes passwords
                'status' => 'active',
            ]
        );

        // 2. Setup Role
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->assignRole($adminRole);

        // 3. Add/Update Administrator details in MasterUserDetail
        MasterUserDetail::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'fname' => 'Administrator',
                'mobile_one' => '9999999999',
                'email_one' => 'admin@byndai.in',
                'desg' => 'System administrator',
            ]
        );
    }
}
