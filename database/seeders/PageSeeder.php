<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Page::create([
            'slug' => 'about',
            'title' => 'About Bynd AI',
            'content' => [
                'page' => 'about',
                'title' => 'About Bynd AI',
                'hero' => [
                    'heading' => 'About Bynd AI',
                    'subheading' => 'Empowering the Future with Intelligent AI Solutions',
                    'background_gradient' => ['#4f46e5', '#9333ea'],
                ],
                'sections' => [
                    [
                        'type' => 'text',
                        'title' => 'Who We Are',
                        'content' => 'Bynd AI is an innovative artificial intelligence platform...',
                    ],
                ],
                'footer' => [
                    'text' => '© 2026 Bynd AI. All Rights Reserved.',
                ],
            ],
        ]);
    }
}
