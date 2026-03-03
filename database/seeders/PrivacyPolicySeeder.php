<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrivacyPolicySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'page' => 'privacy-policy',
            'title' => 'Privacy Policy - Bynd AI',
            'hero' => [
                'heading' => 'Privacy Policy',
                'subheading' => 'Your privacy is important to us at Bynd AI',
                'background_gradient' => ['#4f46e5', '#9333ea'],
            ],
            'meta' => [
                'last_updated' => 'January 2026',
            ],
            'sections' => [
                [
                    'type' => 'text',
                    'title' => '1. Introduction',
                    'content' => 'Bynd AI ("we", "our", or "us") values your privacy. This Privacy Policy explains how we collect, use, and protect your information when you use our platform and services.',
                ],
                [
                    'type' => 'list',
                    'title' => '2. Information We Collect',
                    'content' => 'We may collect the following types of information:',
                    'items' => [
                        'Personal information such as name and email address',
                        'Usage data and interaction logs',
                        'Device and browser information',
                        'Cookies and tracking technologies',
                    ],
                ],
                [
                    'type' => 'list',
                    'title' => '3. How We Use Your Information',
                    'items' => [
                        'To provide and improve our AI services',
                        'To personalize user experience',
                        'To communicate updates and support',
                        'To enhance security and prevent fraud',
                    ],
                ],
                [
                    'type' => 'text',
                    'title' => '4. Data Security',
                    'content' => 'We implement industry-standard security measures to protect your data from unauthorized access, alteration, disclosure, or destruction.',
                ],
                [
                    'type' => 'text',
                    'title' => '5. Third-Party Services',
                    'content' => 'We may use trusted third-party services to operate our platform. These services are obligated to maintain the confidentiality of your data.',
                ],
                [
                    'type' => 'text',
                    'title' => '6. Cookies',
                    'content' => 'Bynd AI uses cookies to enhance your browsing experience and analyze traffic. You can choose to disable cookies in your browser settings.',
                ],
                [
                    'type' => 'text',
                    'title' => '7. Your Rights',
                    'content' => 'You have the right to access, update, or delete your personal information. You may contact us at any time regarding your data.',
                ],
                [
                    'type' => 'text',
                    'title' => '8. Changes to This Policy',
                    'content' => 'We may update this Privacy Policy periodically. Any changes will be reflected on this page with an updated revision date.',
                ],
                [
                    'type' => 'contact',
                    'title' => '9. Contact Us',
                    'content' => 'If you have any questions regarding this Privacy Policy, please contact us at:',
                    'email' => 'support@byndai.com',
                ],
            ],
            'footer' => [
                'text' => '© 2026 Bynd AI. All Rights Reserved.',
            ],
        ];

        DB::table('pages')->updateOrInsert(
            ['slug' => 'privacy-policy'], // condition
            [
                'title' => 'Privacy Policy - Bynd AI',
                'content' => json_encode($data),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
