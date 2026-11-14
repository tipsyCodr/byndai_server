<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\BrandKit;
use App\Models\BusinessInfo;
use App\Models\FlyerGeneration;
use App\Models\MasterUserDetail;
use App\Models\Occasion;
use App\Services\Flyer\FlyerGenerationService;
use App\Services\Flyer\FlyerRetentionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AiFlyerApiController extends Controller
{
    public function __construct(
        protected FlyerGenerationService $generationService,
        protected FlyerRetentionService $retentionService
    ) {}

    /**
     * GET /api/flyers/occasions/today
     * Get occasions for today (or specified date)
     */
    public function todayOccasions(Request $request): JsonResponse
    {
        // Auto-seed if empty
        if (Occasion::count() === 0) {
            try {
                (new \Database\Seeders\AiFlyerSeeder())->run();
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $dateParam = $request->query('date');
        $date = $dateParam ? Carbon::parse($dateParam)->format('m-d') : Carbon::today()->format('m-d');
        $year = $dateParam ? (int) Carbon::parse($dateParam)->format('Y') : (int) Carbon::today()->format('Y');

        $occasions = Occasion::active()
            ->forDate($date, $year)
            ->get();

        // If no occasion today, also fetch the next upcoming 5 occasions as recommendations
        $upcoming = Occasion::active()
            ->where('date', '>=', $date)
            ->orderBy('date', 'asc')
            ->limit(5)
            ->get();

        if ($upcoming->isEmpty()) {
            $upcoming = Occasion::active()->limit(5)->get();
        }

        $quota = $request->user() ? $this->generationService->checkUserQuota($request->user()) : null;

        return response()->json([
            'status' => true,
            'message' => 'Occasions fetched successfully',
            'data' => [
                'date' => $date,
                'has_occasion_today' => $occasions->isNotEmpty(),
                'occasions' => $occasions->isNotEmpty() ? $occasions : $upcoming,
                'upcoming_recommendations' => $upcoming,
                'quota' => $quota,
            ],
        ]);
    }

    /**
     * GET /api/flyers/categories
     * Get predefined special flyer categories, styles, and options
     */
    public function categories(Request $request): JsonResponse
    {
        $categories = [
            [
                'id' => 'good_morning',
                'name' => 'Good Morning',
                'icon' => 'wb_sunny_rounded',
                'description' => 'Positive greetings to start the day with inspiration.',
            ],
            [
                'id' => 'good_night',
                'name' => 'Good Night',
                'icon' => 'nights_stay_rounded',
                'description' => 'Peaceful and calming evening wishes.',
            ],
            [
                'id' => 'motivational_quotes',
                'name' => 'Motivational Quotes',
                'icon' => 'trending_up_rounded',
                'description' => 'High-energy motivational and drive-focused quotes.',
            ],
            [
                'id' => 'inspirational_thoughts',
                'name' => 'Inspirational Thoughts',
                'icon' => 'lightbulb_outline_rounded',
                'description' => 'Wisdom and thought-provoking insights for life.',
            ],
            [
                'id' => 'success_achievement',
                'name' => 'Success and Achievement',
                'icon' => 'emoji_events_rounded',
                'description' => 'Celebrate milestones, victories and business growth.',
            ],
            [
                'id' => 'business_promotion',
                'name' => 'Business Promotion',
                'icon' => 'campaign_rounded',
                'description' => 'Promote products, services, offers, and commercial values.',
            ],
            [
                'id' => 'customer_appreciation',
                'name' => 'Customer Appreciation',
                'icon' => 'volunteer_activism_rounded',
                'description' => 'Thank your customers for their continued loyalty.',
            ],
            [
                'id' => 'thank_you',
                'name' => 'Thank You',
                'icon' => 'handshake_rounded',
                'description' => 'Express gratitude to clients, partners, and audience.',
            ],
            [
                'id' => 'positive_thoughts',
                'name' => 'Positive Thoughts',
                'icon' => 'sentiment_very_satisfied_rounded',
                'description' => 'Uplifting and cheerful vibes for everyday sharing.',
            ],
            [
                'id' => 'custom',
                'name' => 'Custom Flyer',
                'icon' => 'auto_awesome_rounded',
                'description' => 'Design anything you imagine with custom instructions.',
            ],
        ];

        $designStyles = [
            ['id' => 'Modern', 'name' => 'Modern', 'description' => 'Clean contemporary graphics with depth & vibrant lighting.'],
            ['id' => 'Minimal', 'name' => 'Minimal', 'description' => 'Elegant white space, sophisticated geometry & subtle gradients.'],
            ['id' => 'Premium', 'name' => 'Premium', 'description' => 'Gold accents, deep rich colors & luxury commercial finish.'],
            ['id' => 'Corporate', 'name' => 'Corporate', 'description' => 'Polished business aesthetic with sharp geometry.'],
            ['id' => 'Traditional', 'name' => 'Traditional', 'description' => 'Authentic Indian ethnic art, mandalas & celebratory motifs.'],
            ['id' => 'Festive', 'name' => 'Festive', 'description' => 'Sparkling joyous atmosphere with festive bokeh & vibrant warmth.'],
        ];

        $languages = [
            ['id' => 'en', 'name' => 'English'],
            ['id' => 'hi', 'name' => 'Hindi (हिंदी)'],
            ['id' => 'bilingual', 'name' => 'Bilingual (Hindi + English)'],
        ];

        $aspectRatios = [
            ['id' => '1:1', 'name' => 'Square (1:1)', 'description' => 'Feed Post / WhatsApp Status'],
            ['id' => '4:5', 'name' => 'Portrait (4:5)', 'description' => 'Instagram Portrait'],
            ['id' => '9:16', 'name' => 'Story (9:16)', 'description' => 'Story / Reels / Full Screen'],
        ];

        $quota = $request->user() ? $this->generationService->checkUserQuota($request->user()) : null;

        return response()->json([
            'status' => true,
            'message' => 'Flyer categories and options fetched',
            'data' => [
                'categories' => $categories,
                'design_styles' => $designStyles,
                'languages' => $languages,
                'aspect_ratios' => $aspectRatios,
                'quota' => $quota,
            ],
        ]);
    }

    /**
     * POST /api/flyers/generate
     * Request an AI flyer generation
     */
    public function generate(Request $request): JsonResponse
    {
        $input = $request->all();

        // Normalize language
        if (isset($input['language'])) {
            $lang = strtolower(trim($input['language']));
            if ($lang === 'english' || $lang === 'en') {
                $input['language'] = 'en';
            } elseif ($lang === 'hindi' || $lang === 'hi') {
                $input['language'] = 'hi';
            } elseif (str_contains($lang, 'hindi') && str_contains($lang, 'english') || $lang === 'bilingual') {
                $input['language'] = 'bilingual';
            }
        }

        // Support branding_toggles key from frontend
        if (!isset($input['branding_options']) && isset($input['branding_toggles'])) {
            $input['branding_options'] = $input['branding_toggles'];
        }

        $validator = Validator::make($input, [
            'type' => 'required|in:occasion,special,custom',
            'category' => 'required|string|max:100',
            'occasion_id' => 'nullable|exists:occasions,id',
            'language' => 'required|in:en,hi,bilingual',
            'design_style' => 'required|string',
            'aspect_ratio' => 'required|string',
            'custom_prompt' => 'nullable|string|max:1000',
            'branding_options' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error: ' . $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $generation = $this->generationService->createGeneration(
                $request->user(),
                $validator->validated()
            );

            return response()->json([
                'status' => true,
                'message' => 'Flyer generation started successfully',
                'data' => [
                    'id' => $generation->id,
                    'status' => $generation->status,
                    'headline' => $generation->headline,
                    'category' => $generation->category,
                    'created_at' => $generation->created_at->toIso8601String(),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * GET /api/flyers/{id}
     * Check generation status or fetch completed flyer
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $generation = FlyerGeneration::with('occasion')->find($id);

        if (!$generation || $generation->user_id !== $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Flyer not found',
            ], 404);
        }

        $progressMessage = match ($generation->status) {
            'pending' => 'Preparing your design prompt...',
            'processing' => empty($generation->raw_ai_image_path)
                ? 'Creating artwork with AI...'
                : 'Applying business branding and typography...',
            'completed' => 'Your flyer is ready!',
            'failed' => 'Generation failed: ' . ($generation->error_message ?: 'Unknown error'),
            default => 'Loading...',
        };

        return response()->json([
            'status' => true,
            'message' => 'Flyer status retrieved',
            'data' => [
                'id' => $generation->id,
                'type' => $generation->type,
                'category' => $generation->category,
                'headline' => $generation->headline,
                'subheadline' => $generation->subheadline,
                'status' => $generation->status,
                'progress_message' => $progressMessage,
                'error_message' => $generation->error_message,
                'image_url' => $generation->image_url,
                'raw_ai_image_url' => $generation->raw_ai_image_url,
                'language' => $generation->language,
                'design_style' => $generation->design_style,
                'aspect_ratio' => $generation->aspect_ratio,
                'created_at' => $generation->created_at->toIso8601String(),
            ],
        ]);
    }

    /**
     * GET /api/flyers/history
     * Get paginated history of user's generated flyers
     */
    public function history(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 12), 30);

        $flyers = FlyerGeneration::where('user_id', $request->user()->id)
            ->where('status', 'completed')
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Flyer history retrieved successfully',
            'data' => $flyers,
        ]);
    }

    /**
     * POST /api/flyers/{id}/regenerate
     * Regenerate an existing flyer
     */
    public function regenerate(Request $request, int $id): JsonResponse
    {
        $generation = FlyerGeneration::find($id);

        if (!$generation || $generation->user_id !== $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Flyer not found',
            ], 404);
        }

        try {
            $updated = $this->generationService->regenerate($request->user(), $generation);

            return response()->json([
                'status' => true,
                'message' => 'Flyer regeneration started',
                'data' => [
                    'id' => $updated->id,
                    'status' => $updated->status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * DELETE /api/flyers/{id}
     * Delete an individual generated flyer
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $generation = FlyerGeneration::find($id);

        if (!$generation || $generation->user_id !== $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Flyer not found',
            ], 404);
        }

        $this->retentionService->deleteFlyerWithFiles($generation);

        return response()->json([
            'status' => true,
            'message' => 'Flyer deleted successfully',
        ]);
    }

    /**
     * GET /api/flyers/brand-kit
     * Get or initialize Brand Kit for user
     */
    public function getBrandKit(Request $request): JsonResponse
    {
        $user = $request->user();
        $brandKit = BrandKit::where('user_id', $user->id)->first();

        // If not created yet, populate initial defaults from profile
        if (!$brandKit) {
            $businessInfo = BusinessInfo::where('user_id', $user->id)->first();
            $userDetail = MasterUserDetail::where('user_id', $user->id)->first();

            $brandKit = BrandKit::create([
                'user_id' => $user->id,
                'business_name' => $businessInfo?->legal_name ?: $userDetail?->fname ?: $user->name,
                'business_category' => $userDetail?->busi_type ?: 'Business',
                'logo_path' => $userDetail?->profile_logo,
                'contact_number' => $userDetail?->mobile_one ?: $user->mobile,
                'whatsapp_number' => $userDetail?->mobile_one ?: $user->mobile,
                'email' => $businessInfo?->contact_email ?: $user->email,
                'website' => $businessInfo?->website_url,
                'address' => $businessInfo?->address ?: $userDetail?->address,
                'primary_color' => '#1F51E5',
                'secondary_color' => '#25D366',
                'preferred_language' => 'bilingual',
                'preferred_style' => 'Modern',
                'preferred_aspect_ratio' => '1:1',
                'footer_style' => 0,
                'show_logo' => true,
                'show_name' => true,
                'show_contact' => true,
                'show_whatsapp' => true,
                'show_address' => true,
                'show_website' => true,
            ]);
        }

        $logoUrl = null;
        if ($brandKit->logo_path) {
            $logoUrl = str_starts_with($brandKit->logo_path, 'http')
                ? $brandKit->logo_path
                : Storage::disk('public')->url($brandKit->logo_path);
        }

        return response()->json([
            'status' => true,
            'message' => 'Brand kit retrieved',
            'data' => array_merge($brandKit->toArray(), [
                'logo_url' => $logoUrl,
            ]),
        ]);
    }

    /**
     * POST /api/flyers/brand-kit
     * Update Brand Kit for user
     */
    public function updateBrandKit(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'business_name' => 'nullable|string|max:150',
            'business_category' => 'nullable|string|max:100',
            'contact_number' => 'nullable|string|max:30',
            'whatsapp_number' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|string|max:200',
            'address' => 'nullable|string|max:300',
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'preferred_language' => 'nullable|string|in:en,hi,bilingual',
            'preferred_style' => 'nullable|string|in:Modern,Minimal,Premium,Corporate,Traditional,Festive',
            'preferred_aspect_ratio' => 'nullable|string|in:1:1,4:5,9:16',
            'footer_style' => 'nullable|integer',
            'show_logo' => 'nullable|boolean',
            'show_name' => 'nullable|boolean',
            'show_contact' => 'nullable|boolean',
            'show_whatsapp' => 'nullable|boolean',
            'show_address' => 'nullable|boolean',
            'show_website' => 'nullable|boolean',
            'logo' => 'nullable|image|max:5120', // max 5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $brandKit = BrandKit::firstOrNew(['user_id' => $user->id]);
        $validated = $validator->validated();

        // Handle logo file upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('brand_logos', 'public');
            $validated['logo_path'] = $path;
        }

        unset($validated['logo']);

        $brandKit->fill($validated);
        $brandKit->save();

        $logoUrl = null;
        if ($brandKit->logo_path) {
            $logoUrl = str_starts_with($brandKit->logo_path, 'http')
                ? $brandKit->logo_path
                : Storage::disk('public')->url($brandKit->logo_path);
        }

        return response()->json([
            'status' => true,
            'message' => 'Brand kit updated successfully',
            'data' => array_merge($brandKit->toArray(), [
                'logo_url' => $logoUrl,
            ]),
        ]);
    }
}

