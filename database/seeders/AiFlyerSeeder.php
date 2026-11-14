<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AiFlyerSetting;
use App\Models\Occasion;

class AiFlyerSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Default Settings
        $settings = [
            'ai_flyer_enabled' => ['value' => '1', 'description' => 'Master toggle for AI Flyer Generation'],
            'openai_model' => ['value' => 'gpt-image-2.6-flare', 'description' => 'Image generation model (gpt-image-2.6-flare / dall-e-3)'],
            'image_quality' => ['value' => 'standard', 'description' => 'Default image quality (standard / hd)'],
            'default_aspect_ratio' => ['value' => '1:1', 'description' => 'Default aspect ratio for generation'],
            'max_saved_flyers_per_user' => ['value' => '10', 'description' => 'Maximum completed flyers kept per user'],
            'daily_generation_limit_per_user' => ['value' => '5', 'description' => 'Daily flyer generation quota per user'],
            'retention_cleanup_frequency' => ['value' => 'daily', 'description' => 'Retention cleanup frequency'],
        ];

        foreach ($settings as $key => $data) {
            AiFlyerSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $data['value'], 'description' => $data['description']]
            );
        }

        // 2. Pre-populated Indian Festivals and Occasions
        $occasions = [
            // January
            ['name' => 'New Year', 'date' => '01-01', 'region' => 'International', 'language' => 'bilingual', 'description' => 'Celebrate the beginning of a fresh new year with optimism and joy.'],
            ['name' => 'Makar Sankranti', 'date' => '01-14', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Festival of kites, harvest and sun transition.'],
            ['name' => 'Pongal', 'date' => '01-15', 'region' => 'South India', 'language' => 'bilingual', 'description' => 'Tamil harvest festival offering thanks to nature.'],
            ['name' => 'Republic Day', 'date' => '01-26', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Commemorating the Constitution of India with patriotic pride.'],

            // February
            ['name' => 'World Cancer Day', 'date' => '02-04', 'region' => 'International', 'language' => 'en', 'description' => 'Raising global awareness and encouraging prevention.'],
            ['name' => 'Maha Shivratri', 'date' => '02-26', 'region' => 'National', 'language' => 'bilingual', 'description' => 'The great night of Shiva celebrating spiritual awakening.'],
            ['name' => 'National Science Day', 'date' => '02-28', 'region' => 'National', 'language' => 'en', 'description' => 'Commemorating the discovery of Raman Effect by C.V. Raman.'],

            // March
            ['name' => 'International Women\'s Day', 'date' => '03-08', 'region' => 'International', 'language' => 'bilingual', 'description' => 'Honoring the social, economic, cultural achievements of women.'],
            ['name' => 'Holi', 'date' => '03-14', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Festival of vibrant colors, love and triumph of good.'],
            ['name' => 'World Water Day', 'date' => '03-22', 'region' => 'International', 'language' => 'en', 'description' => 'Advocating for the sustainable management of freshwater.'],
            ['name' => 'Eid-ul-Fitr', 'date' => '03-31', 'region' => 'International', 'language' => 'bilingual', 'description' => 'Blessed celebration marking the end of Ramadan.'],

            // April
            ['name' => 'World Health Day', 'date' => '04-07', 'region' => 'International', 'language' => 'en', 'description' => 'Promoting global health awareness and well-being.'],
            ['name' => 'Baisakhi / Ambedkar Jayanti', 'date' => '04-14', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Harvest festival and birth anniversary of Dr. B.R. Ambedkar.'],
            ['name' => 'Earth Day', 'date' => '04-22', 'region' => 'International', 'language' => 'bilingual', 'description' => 'Demonstrating support for environmental protection.'],

            // May
            ['name' => 'International Workers\' Day', 'date' => '05-01', 'region' => 'International', 'language' => 'bilingual', 'description' => 'Celebrating the contributions and rights of workers.'],
            ['name' => 'Mother\'s Day', 'date' => '05-11', 'region' => 'International', 'language' => 'bilingual', 'description' => 'Honoring mothers and motherhood with love and gratitude.'],
            ['name' => 'Buddha Purnima', 'date' => '05-12', 'region' => 'International', 'language' => 'bilingual', 'description' => 'Commemorating the birth and enlightenment of Gautama Buddha.'],

            // June
            ['name' => 'World Environment Day', 'date' => '06-05', 'region' => 'International', 'language' => 'bilingual', 'description' => 'Encouraging worldwide action to protect our environment.'],
            ['name' => 'Father\'s Day', 'date' => '06-15', 'region' => 'International', 'language' => 'bilingual', 'description' => 'Celebrating fathers and father figures.'],
            ['name' => 'International Yoga Day', 'date' => '06-21', 'region' => 'International', 'language' => 'bilingual', 'description' => 'Promoting physical, mental, and spiritual harmony through yoga.'],

            // July
            ['name' => 'National Doctor\'s Day', 'date' => '07-01', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Expressing gratitude to healthcare professionals.'],
            ['name' => 'World Youth Skills Day', 'date' => '07-15', 'region' => 'International', 'language' => 'en', 'description' => 'Equipping young people with skills for employment.'],
            ['name' => 'Kargil Vijay Diwas', 'date' => '07-26', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Remembering the brave martyrs of the Kargil War.'],

            // August
            ['name' => 'Friendship Day', 'date' => '08-03', 'region' => 'International', 'language' => 'bilingual', 'description' => 'Celebrating meaningful bonds of friendship.'],
            ['name' => 'Raksha Bandhan', 'date' => '08-09', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Sacred bond of protection and affection between siblings.'],
            ['name' => 'Independence Day', 'date' => '08-15', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Celebrating India\'s freedom and democratic heritage.'],
            ['name' => 'Janmashtami', 'date' => '08-16', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Celebrating the birth of Lord Krishna with devotion and joy.'],
            ['name' => 'Ganesh Chaturthi', 'date' => '08-27', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Welcoming Lord Ganesha, the remover of obstacles.'],

            // September
            ['name' => 'Teachers\' Day', 'date' => '09-05', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Honoring educators who shape society and minds.'],
            ['name' => 'Hindi Diwas', 'date' => '09-14', 'region' => 'National', 'language' => 'hi', 'description' => 'Celebrating the Hindi language and cultural identity.'],
            ['name' => 'Engineer\'s Day', 'date' => '09-15', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Remembering Sir M. Visvesvaraya and honoring engineers.'],
            ['name' => 'World Tourism Day', 'date' => '09-27', 'region' => 'International', 'language' => 'en', 'description' => 'Promoting cultural exchange through global travel.'],

            // October
            ['name' => 'Gandhi Jayanti', 'date' => '10-02', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Remembering Mahatma Gandhi and the philosophy of non-violence.'],
            ['name' => 'Navratri Begins', 'date' => '10-01', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Nine nights of divine feminine energy and devotion.'],
            ['name' => 'Dussehra / Vijayadashami', 'date' => '10-10', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Victory of good over evil, celebrated across India.'],
            ['name' => 'Diwali / Deepawali', 'date' => '10-20', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Festival of lights celebrating prosperity, hope and joy.'],

            // November
            ['name' => 'Guru Nanak Jayanti', 'date' => '11-05', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Celebrating the sacred birth of Guru Nanak Dev Ji.'],
            ['name' => 'National Education Day', 'date' => '11-11', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Commemorating Maulana Abul Kalam Azad and universal education.'],
            ['name' => 'Children\'s Day', 'date' => '11-14', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Celebrating the innocence and future of children.'],
            ['name' => 'Constitution Day (Samvidhan Diwas)', 'date' => '11-26', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Commemorating the adoption of the Constitution of India.'],

            // December
            ['name' => 'Indian Navy Day', 'date' => '12-04', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Honoring the guardians of our nation\'s maritime borders.'],
            ['name' => 'Human Rights Day', 'date' => '12-10', 'region' => 'International', 'language' => 'en', 'description' => 'Championing inalienable fundamental rights for all people.'],
            ['name' => 'National Farmers Day (Kisan Diwas)', 'date' => '12-23', 'region' => 'National', 'language' => 'bilingual', 'description' => 'Honoring the hardworking farmers who feed the nation.'],
            ['name' => 'Christmas Day', 'date' => '12-25', 'region' => 'International', 'language' => 'bilingual', 'description' => 'Celebrating the joyful spirit of Christmas and peace.'],
        ];

        foreach ($occasions as $item) {
            Occasion::updateOrCreate(
                ['name' => $item['name'], 'date' => $item['date']],
                [
                    'description' => $item['description'],
                    'region' => $item['region'],
                    'language' => $item['language'],
                    'is_active' => true,
                ]
            );
        }
    }
}

