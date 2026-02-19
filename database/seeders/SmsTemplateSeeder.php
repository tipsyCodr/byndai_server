<?php

namespace Database\Seeders;

use App\Models\SmsTemplate;
use Illuminate\Database\Seeder;

class SmsTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SmsTemplate::updateOrCreate(
            ['section' => 'login_otp'],
            [
                'message' => json_encode([
                    'message' => 'Your verification code is {#var#} for login authentication. This code is confidential and valid for limited time only. Do not disclose it. – Path Idea Multiskill',
                    'DLTTemplateId' => '1707173926081442118'
                ]),
                'status' => true,
            ]
        );
    }
}
