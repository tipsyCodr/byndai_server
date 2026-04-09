<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\FlyerCategory;
use App\Models\Flyer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FlyerApiController extends Controller
{
    public function index()
    {
        $categories = FlyerCategory::active()
            ->with(['flyers' => function($q) {
                $q->active();
            }])
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Flyer categories fetched successfully',
            'data' => $categories
        ]);
    }

    public function show($id)
    {
        $flyers = Flyer::active()
            ->where('category_id', $id)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Flyers fetched successfully',
            'data' => $flyers
        ]);
    }

    public function upcoming()
    {
        $currentMonth = Carbon::now()->format('m');
        $currentDay = Carbon::now()->format('d');

        // Fetch festive categories for the current month or next
        $categories = FlyerCategory::active()
            ->where('type', 'festive')
            ->where('date', '>=', "$currentMonth-$currentDay")
            ->orderBy('date', 'asc')
            ->with(['flyers' => function($q) {
                $q->active();
            }])
            ->limit(5)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Upcoming festive flyers fetched successfully',
            'data' => $categories
        ]);
    }
}
