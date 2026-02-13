<?php

namespace App\Http\Controllers;

use App\Models\MasterLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MasterLinkController extends Controller
{
    // ✅ 1) List all master links (only logged-in user)
    public function index(Request $request)
    {
        $links = MasterLink::where('user_id', $request->user()->id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $links
        ]);
    }

    // ✅ 2) Store (Create)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:150',
            'link'   => 'required|string|max:250',
            'status' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $link = MasterLink::create([
            'user_id' => $request->user()->id, // ✅ important
            'name'    => $request->name,
            'link'    => $request->link,
            'status'  => $request->status ?? 1,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Master Link created successfully',
            'data' => $link
        ]);
    }

    // ✅ 3) Show single (only logged-in user)
    public function show(Request $request, $id)
    {
        $link = MasterLink::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$link) {
            return response()->json([
                'status' => false,
                'message' => 'Master Link not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $link
        ]);
    }

    // ✅ 4) Update (only logged-in user)
    public function update(Request $request, $id)
    {
        $link = MasterLink::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$link) {
            return response()->json([
                'status' => false,
                'message' => 'Master Link not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:150',
            'link'   => 'required|string|max:250',
            'status' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $link->update([
            'name'   => $request->name,
            'link'   => $request->link,
            'status' => $request->status ?? $link->status,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Master Link updated successfully',
            'data' => $link
        ]);
    }

    // ✅ 5) Delete (only logged-in user)
    public function destroy(Request $request, $id)
    {
        $link = MasterLink::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$link) {
            return response()->json([
                'status' => false,
                'message' => 'Master Link not found'
            ], 404);
        }

        $link->delete();

        return response()->json([
            'status' => true,
            'message' => 'Master Link deleted successfully'
        ]);
    }
}
