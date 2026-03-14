<?php

namespace App\Http\Controllers\frontend\site;

use App\Http\Controllers\Controller;
use App\Models\UserReview;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'to_user_id'    => 'required|exists:users,id',
            'rating'        => 'required|integer|min:1|max:5',
            'message'       => 'nullable|string|max:1000',
            'reviewer_name' => 'nullable|string|max:100',
        ]);

        $fromUserId = auth()->id() ?? null;

        // Prevent duplicate review from same logged-in user
        if ($fromUserId) {
            $exists = UserReview::where('to_user_id', $request->to_user_id)
                ->where('from_user_id', $fromUserId)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already submitted a review.',
                ], 409);
            }
        }

        UserReview::create([
            'to_user_id'    => $request->to_user_id,
            'from_user_id'  => $fromUserId,
            'reviewer_name' => $fromUserId ? null : ($request->reviewer_name ?: null), // null = anonymous
            'rating'        => $request->rating,
            'message'       => $request->message,
        ]);

        $avg   = UserReview::where('to_user_id', $request->to_user_id)->avg('rating');
        $count = UserReview::where('to_user_id', $request->to_user_id)->count();

        return response()->json([
            'success'        => true,
            'message'        => 'Thank you for your review!',
            'average_rating' => round($avg, 1),
            'review_count'   => $count,
        ]);
    }
    public function generateReview(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'rating'     => 'nullable|integer|min:1|max:5',
        ]);

        $user = \App\Models\User::with(['masterUser', 'businesses', 'businesses.industry'])->find($request->to_user_id);
        $name = $user->masterUser?->fname ?? $user->user_name;
        $rating = $request->rating ?? 5;

        // Fetch context for better review
        $businessNames = $user->businesses->pluck('legal_name')->filter()->unique()->implode(', ');
        $industries = $user->businesses->map(fn($b) => $b->industry?->name)->filter()->unique()->implode(', ');
        $businessDesc = $user->businesses->pluck('description')->filter()->first();
        
        $products = \App\Models\Product::where('user_id', $user->id)->limit(3)->pluck('product_name')->implode(', ');

        $context = "";
        if ($businessNames) $context .= "Business: {$businessNames}. ";
        if ($industries) $context .= "Industry: {$industries}. ";
        if ($products) $context .= "Products/Services: {$products}. ";
        if ($businessDesc) $context .= "Description: " . \Illuminate\Support\Str::limit($businessDesc, 100) . ". ";

        $apiKey = env('OPENAI_API_SECRET_KEY');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API Configuration error.',
            ], 500);
        }

        $prompt = "Write a short, professional, and positive review for {$name}. The rating is {$rating} stars. Context: {$context} Mention specific business or products if relevant. Keep it under 250 characters.";

        try {
            $response = \Illuminate\Support\Facades\Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a helpful assistant that generates specific, professional reviews based on provided business context.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'temperature' => 0.7,
                ]);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate review content.',
                ], 500);
            }

            $result = $response->json();
            $content = trim($result['choices'][0]['message']['content'] ?? '', '"');

            return response()->json([
                'success' => true,
                'content' => $content,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating review.',
            ], 500);
        }
    }
}
