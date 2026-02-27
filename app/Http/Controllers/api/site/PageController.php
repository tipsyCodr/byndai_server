<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->first();

        if (!$page) {
            return response()->json([
                'status' => false,
                'message' => 'Page not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $page
        ]);
    }
}