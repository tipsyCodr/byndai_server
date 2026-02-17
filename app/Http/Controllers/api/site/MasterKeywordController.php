<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\MasterKeyword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterKeywordController extends Controller
{
    // ✅ GET + POST same function (like your code)
    public function index(Request $r)
    {
        $user = Auth::user();

        // ✅ GET keywords list
        if ($r->isMethod('get')) {
            $keywords = MasterKeyword::select('id', 'keywords')
                ->where('user_id', $user->id)
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $keywords,
            ], 200);
        }

        // ✅ POST create keyword
        if ($r->isMethod('post')) {

            $data = $r->validate([
                'title' => 'required|string|max:100',
            ]);

            // ✅ duplicate check (case-insensitive)
            $keywordExists = MasterKeyword::whereRaw('LOWER(keywords) = ?', [strtolower($data['title'])])
                ->where('user_id', $user->id)
                ->exists();

            if ($keywordExists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Keyword already exists',
                ], 409);
            }

            $keyword = MasterKeyword::create([
                'keywords' => $data['title'],
                'user_id' => $user->id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Keyword created successfully',
                'data' => $keyword,
            ], 201);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid request method',
        ], 405);
    }

    // ✅ DELETE keyword
    public function deleteKeyword(Request $r, $id)
    {
        $user = Auth::user();

        $keyword = MasterKeyword::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $keyword) {
            return response()->json([
                'status' => false,
                'message' => 'Keyword not found',
            ], 404);
        }

        $keyword->delete();

        return response()->json([
            'status' => true,
            'message' => 'Successfully deleted keyword!',
        ], 200);
    }

    // ✅ SEARCH users by keywords (same as your model function)
    public function getUsersByKeywords(Request $r)
    {
        $data_provider = MasterKeyword::getUsersProfile($r);

        return response()->json([
            'status' => true,
            'data' => $data_provider,
        ], 200);
    }

    public function getKeywordsByUserId($user_id)
    {
        // ✅ keywords list by user_id
        $keywords = MasterKeyword::select('id', 'keywords')
            ->where('user_id', $user_id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $keywords,
        ], 200);
    }

    public function updateKeyword(Request $r, $id)
    {
        $user = Auth::user();

        $data = $r->validate([
            'title' => 'required|string|max:100',
        ]);

        // ✅ keyword find (only logged-in user)
        $keyword = MasterKeyword::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $keyword) {
            return response()->json([
                'status' => false,
                'message' => 'Keyword not found',
            ], 404);
        }

        // ✅ duplicate check (same user)
        $alreadyExists = MasterKeyword::where('user_id', $user->id)
            ->whereRaw('LOWER(keywords) = ?', [strtolower($data['title'])])
            ->where('id', '!=', $id)
            ->exists();

        if ($alreadyExists) {
            return response()->json([
                'status' => false,
                'message' => 'Keyword already exists',
            ], 409);
        }

        // ✅ update
        $keyword->update([
            'keywords' => $data['title'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Keyword updated successfully',
            'data' => $keyword,
        ], 200);
    }
}
