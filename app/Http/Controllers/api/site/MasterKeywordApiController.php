<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\MasterKeyword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterKeywordApiController extends Controller
{
    public function index(Request $r)
    {
        $user = Auth::user();
        if ($r->isMethod('get')) {
            $keywords = MasterKeyword::select('id', 'keywords')->where('user_id', $user->id)->get();
            return response()->json(['status' => true, 'data' => $keywords], 200);
        }
        if ($r->isMethod('post')) {
            $data = $r->validate([
                'title' => 'required|string|max:100'
            ]);
            $keywordExists = MasterKeyword::whereRaw('LOWER(keywords) = ?', [strtolower($data['title'])])->where('user_id', $user->id)->exists();
            if (!$keywordExists) {
                $keyword = MasterKeyword::create(['keywords' => $data['title'], 'user_id' => $user->id]);
                return response()->json(['status' => true, 'message' => 'Keyword created successfully', 'data' => $keyword], 201);
            }
            return response()->json(['status' => false, 'message' => 'Keyword already exists'], 409);
        }
        return response()->json(['status' => false, 'message' => 'Invalid request method'], 405);
    }
    public function deleteKeyword($id)
    {
        $keywords = MasterKeyword::findorFail($id);
        $keywords->delete();
        return response()->json(['status' => true, 'message' => 'successfully deleted keywords!'], 200);
    }
    public function getUsersByKeywords(Request $r)
    {
        $data_provider = MasterKeyword::getUsersProfile($r);
        return response()->json(['status' => true, 'data' => $data_provider]);
    }
}
