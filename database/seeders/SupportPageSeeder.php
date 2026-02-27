<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupportPageSeeder extends Seeder
{
    public function run(): void
    {
        $supportContent = [
            "hero" => [
                "heading" => "Bynd AI Support",
                "subheading" => "We’re here to help you anytime.",
                "background_gradient" => ["#4f46e5", "#7c3aed"]
            ],
            "sections" => [
                [
                    "type" => "text",
                    "title" => "How Can We Help?",
                    "content" => "If you are facing any issue while using Bynd AI, please check our FAQs below or contact our support team directly."
                ],
                [
                    "type" => "faq",
                    "title" => "Frequently Asked Questions",
                    "items" => [
                        [
                            "question" => "How do I reset my password?",
                            "answer" => "You can reset your password by clicking on the 'Forgot Password' option on the login screen."
                        ],
                        [
                            "question" => "How do I contact support?",
                            "answer" => "You can email us directly at support@byndai.com or use the contact form below."
                        ],
                        [
                            "question" => "Is my data secure?",
                            "answer" => "Yes, Bynd AI uses secure encryption and follows best industry practices to protect your data."
                        ]
                    ]
                ],
                [
                    "type" => "contact_form",
                    "title" => "Contact Support",
                    "fields" => [
                        [
                            "type" => "text",
                            "name" => "name",
                            "label" => "Your Name",
                            "required" => true
                        ],
                        [
                            "type" => "email",
                            "name" => "email",
                            "label" => "Your Email",
                            "required" => true
                        ],
                        [
                            "type" => "textarea",
                            "name" => "message",
                            "label" => "Describe your issue...",
                            "required" => true
                        ]
                    ],
                    "support_email" => "support@byndai.com"
                ]
            ],
            "footer" => [
                "text" => "© 2026 Bynd AI. All Rights Reserved."
            ]
        ];

        // 🔥 Update if exists, otherwise insert
        DB::table('pages')->updateOrInsert(
            ['slug' => 'support'],
            [
                'title' => 'Support - Bynd AI',
                'content' => json_encode($supportContent),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}