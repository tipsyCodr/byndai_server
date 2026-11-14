<?php

namespace App\Services\Flyer;

use App\Models\BrandKit;
use App\Models\Occasion;

class FlyerPromptBuilder
{
    /**
     * Build prompt, headline, and subheadline
     */
    public function build(
        string $type,
        string $category,
        ?Occasion $occasion,
        string $language,
        string $designStyle,
        string $aspectRatio,
        ?string $customPrompt,
        ?BrandKit $brandKit
    ): array {
        $headlines = $this->resolveHeadlines($type, $category, $occasion, $language);
        $headline = $headlines['headline'];
        $subheadline = $headlines['subheadline'];

        // Determine visual style instructions
        $stylePrompt = match (strtolower($designStyle)) {
            'minimal' => 'Clean, sleek minimalism with high white space, subtle gradients, sophisticated geometry and elegant simplicity.',
            'premium' => 'Ultra-luxurious aesthetic with rich gold accents, deep velvety lighting, exquisite textures, cinematic depth of field and opulent detailing.',
            'corporate' => 'Polished modern corporate design, professional business aesthetic, clean geometric lines, trustworthy tech-forward atmosphere and modern lighting.',
            'traditional' => 'Rich Indian cultural motif, intricate ethnic mandalas, marigold floristry, authentic traditional celebratory elements, brass diyas and auspicious royal tones.',
            'festive' => 'Vibrant celebration with joyful energetic atmosphere, festive sparkles, glowing bokeh lights, radiant celebratory decorations and uplifting warmth.',
            default => 'Modern graphic design, dynamic lighting, stylish depth, clean contemporary commercial poster aesthetic with stunning 3D visual composition.',
        };

        // Aspect ratio framing guidance
        $aspectRatioGuidance = match ($aspectRatio) {
            '9:16' => 'Vertical 9:16 story poster composition. Position the primary visual centerpiece in the middle third.',
            '4:5' => 'Vertical 4:5 portrait social media flyer layout. Centered dramatic visual focus.',
            default => 'Square 1:1 format. Well-balanced focal artwork positioned centrally.',
        };

        // Occasion / Category Subject
        $subject = '';
        if ($type === 'occasion' && $occasion) {
            $subject = "Celebration of {$occasion->name}. {$occasion->description}";
            if ($occasion->sample_prompts) {
                $subject .= " Visual theme: {$occasion->sample_prompts}.";
            }
        } else {
            $subject = match (strtolower($category)) {
                'good morning' => 'Peaceful, inspiring morning sunrise with gentle golden sun rays, blooming dewy blossoms, steaming ceramic coffee cup, serene tranquil outdoor nature.',
                'good night' => 'Dreamy enchanting night sky with shimmering constellation stars, serene crescent moon, calm clouds, peaceful ethereal ambient blue and violet glow.',
                'motivational quotes' => 'Empowering triumph visual featuring ascending mountain peaks, glowing horizon dawn, metaphor of growth, strength and victorious summit under golden skies.',
                'inspirational thoughts' => 'Thoughtful, artistic imagery symbolizing wisdom, boundless innovation, glowing neural lightbulb concept, floating light particles in majestic open skies.',
                'success and achievement' => 'Celebration of triumph and milestone achievement, radiant golden trophy or laurel emblem, celebratory bursts, majestic leadership atmosphere.',
                'business promotion' => 'Commercial promotional spotlight, dynamic 3D commercial display pedestal, sleek modern retail backdrop, vibrant gift or spotlight presentation.',
                'customer appreciation' => 'Warm appreciation celebration, golden ribbon heart or gift box, premium confetti, welcoming hospitable atmosphere expressing genuine gratitude.',
                'thank you' => 'Heartfelt gratitude design with elegant blossoming floral bouquet, shimmering golden accents, warm ambient glowing background.',
                'positive thoughts' => 'Uplifting joyful composition with radiant sunflower motifs, warm sunshine, blue skies, vibrant positive energy and soaring birds.',
                default => 'Inspiring commercial flyer artwork centered around ' . $category . '.',
            };
        }

        // Color palette guidance based on brand kit
        $colorGuidance = '';
        if ($brandKit && ($brandKit->primary_color || $brandKit->secondary_color)) {
            $colorGuidance = "Infuse subtle color harmony inspired by tones {$brandKit->primary_color} and {$brandKit->secondary_color}.";
        }

        // Custom prompt addition
        $customAddition = '';
        if ($customPrompt && trim($customPrompt) !== '') {
            $customAddition = "Specific creative request: " . trim($customPrompt) . ".";
        }

        // Full AI prompt
        $fullPrompt = "Professional high-end commercial graphic flyer background artwork. Theme: {$subject}. "
            . "Style: {$stylePrompt} {$aspectRatioGuidance} {$colorGuidance} {$customAddition} "
            . "COMPOSITION REQUIREMENT: Create a stunning central visual motif. Keep the upper 15% and bottom 20% of the canvas clean, uncluttered, and suitable for typographic overlays. "
            . "NO distorted logos, NO messy gibberish text, ultra-sharp 8k resolution, cinematic lighting, masterwork commercial illustration.";

        return [
            'prompt' => $fullPrompt,
            'headline' => $headline,
            'subheadline' => $subheadline,
        ];
    }

    /**
     * Determine headline and subheadline based on occasion/category & language
     */
    protected function resolveHeadlines(string $type, string $category, ?Occasion $occasion, string $language): array
    {
        $lang = strtolower($language);

        if ($type === 'occasion' && $occasion) {
            $name = $occasion->name;
            if ($lang === 'hi') {
                return [
                    'headline' => "{$name} की हार्दिक शुभकामनाएं",
                    'subheadline' => "आप और आपके परिवार को सुख, शांति और समृद्धि की मंगलकामनाएं।",
                ];
            } elseif ($lang === 'bilingual') {
                return [
                    'headline' => "Happy {$name} • हार्दिक शुभकामनाएं",
                    'subheadline' => "Wishing you and your family abundance, joy and prosperity.",
                ];
            } else {
                return [
                    'headline' => "Happy {$name}!",
                    'subheadline' => "Wishing you joy, good health, and success on this auspicious occasion.",
                ];
            }
        }

        // Special categories
        return match (strtolower($category)) {
            'good morning' => match ($lang) {
                'hi' => ['headline' => 'शुभ प्रभात', 'subheadline' => 'आपका आज का दिन मंगलमय, सफल और ऊर्जावान हो।'],
                'bilingual' => ['headline' => 'Good Morning • शुभ प्रभात', 'subheadline' => 'May your day be filled with positive energy and success.'],
                default => ['headline' => 'Good Morning!', 'subheadline' => 'Start each day with a grateful heart and positive mindset.'],
            },
            'good night' => match ($lang) {
                'hi' => ['headline' => 'शुभ रात्रि', 'subheadline' => 'मीठे सपनों और शांतिपूर्ण नींद के साथ कल एक नई शुरुआत हो।'],
                'bilingual' => ['headline' => 'Good Night • शुभ रात्रि', 'subheadline' => 'Rest well and wake up refreshed for new opportunities.'],
                default => ['headline' => 'Good Night!', 'subheadline' => 'Rest well, recharge your soul, and dream big tonight.'],
            },
            'motivational quotes' => match ($lang) {
                'hi' => ['headline' => 'दृढ़ संकल्प ही विजय है', 'subheadline' => 'कठिनाइयों से मत डरो, यही तुम्हारी असली ताकत बनाती हैं।'],
                'bilingual' => ['headline' => 'Stay Motivated • आगे बढ़ते रहें', 'subheadline' => 'Small daily improvements over time lead to stunning results.'],
                default => ['headline' => 'Believe & Achieve', 'subheadline' => 'Every accomplishment starts with the decision to try.'],
            },
            'inspirational thoughts' => match ($lang) {
                'hi' => ['headline' => 'सकारात्मक सोचें, आगे बढ़ें', 'subheadline' => 'आपकी सोच ही आपकी दुनिया का निर्माण करती है।'],
                'bilingual' => ['headline' => 'Inspire Greatness', 'subheadline' => 'The future belongs to those who believe in the beauty of their dreams.'],
                default => ['headline' => 'Dream Without Limits', 'subheadline' => 'Your only limit is the one you set in your mind.'],
            },
            'success and achievement' => match ($lang) {
                'hi' => ['headline' => 'सफलता का उत्सव', 'subheadline' => 'कड़ी मेहनत और समर्पण हमेशा श्रेष्ठ परिणाम लाते हैं।'],
                'bilingual' => ['headline' => 'Celebrate Success', 'subheadline' => 'Success is the sum of small efforts repeated day in and day out.'],
                default => ['headline' => 'Celebrate Your Wins', 'subheadline' => 'Excellence is not an act, but a habit. Keep reaching higher.'],
            },
            'business promotion' => match ($lang) {
                'hi' => ['headline' => 'विशेष व्यावसायिक पेशकश', 'subheadline' => 'आपके विश्वास और संतुष्टि के लिए हमारी प्रतिबद्धता।'],
                'bilingual' => ['headline' => 'Exclusive Offer • विशेष ऑफर', 'subheadline' => 'Premium quality services crafted for your success.'],
                default => ['headline' => 'Special Announcement', 'subheadline' => 'Delivering premium excellence and trusted value every day.'],
            },
            'customer appreciation' => match ($lang) {
                'hi' => ['headline' => 'हमारे मूल्यवान ग्राहक', 'subheadline' => 'आपके विश्वास और सहयोग के लिए दिल से धन्यवाद।'],
                'bilingual' => ['headline' => 'We Value You • धन्यवाद', 'subheadline' => 'Thank you for being an essential part of our journey.'],
                default => ['headline' => 'We Appreciate You!', 'subheadline' => 'Thank you for choosing us and trusting our service.'],
            },
            'thank you' => match ($lang) {
                'hi' => ['headline' => 'हार्दिक धन्यवाद', 'subheadline' => 'आपके निरंतर साथ और सहयोग के लिए हम आभारी हैं।'],
                'bilingual' => ['headline' => 'Thank You • धन्यवाद', 'subheadline' => 'Grateful for your continued trust and partnership.'],
                default => ['headline' => 'Thank You!', 'subheadline' => 'We truly appreciate your support and companionship.'],
            },
            'positive thoughts' => match ($lang) {
                'hi' => ['headline' => 'सकारात्मक ऊर्जा', 'subheadline' => 'सदा मुस्कुराते रहिए, जीवन बहुत सुंदर है।'],
                'bilingual' => ['headline' => 'Positive Vibes Only', 'subheadline' => 'Radiate kindness and the world will brighten around you.'],
                default => ['headline' => 'Choose Positivity', 'subheadline' => 'Keep your face always toward the sunshine, and shadows will fall behind you.'],
            },
            default => [
                'headline' => $category,
                'subheadline' => 'Crafted with passion for your business growth.',
            ],
        };
    }
}

