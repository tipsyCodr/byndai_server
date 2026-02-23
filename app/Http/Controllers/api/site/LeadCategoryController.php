<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\LeadCategory;
use Illuminate\Http\Request;

class LeadCategoryController extends Controller
{
    // ✅ Create Lead Category
    public function create(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'category' => 'required|string|max:255',
        ]);

        $leadCategory = LeadCategory::create([
            'user_id' => $request->user_id,
            'category' => $request->category,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Lead Category created successfully',
            'data' => $leadCategory,
        ], 201);
    }

    // ✅ Update Lead Category
    public function update(Request $request, $id)
    {
        $leadCategory = LeadCategory::find($id);

        if (! $leadCategory) {
            return response()->json([
                'status' => false,
                'message' => 'Lead Category not found',
            ], 404);
        }

        $request->validate([
            'category' => 'required|string|max:255',
        ]);

        $leadCategory->update([
            'category' => $request->category,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Lead Category updated successfully',
            'data' => $leadCategory,
        ]);
    }

    // ✅ Delete Lead Category
    public function delete($id)
    {
        $leadCategory = LeadCategory::find($id);

        if (! $leadCategory) {
            return response()->json([
                'status' => false,
                'message' => 'Lead Category not found',
            ], 404);
        }

        $leadCategory->delete();

        return response()->json([
            'status' => true,
            'message' => 'Lead Category deleted successfully',
        ]);
    }

    // ✅ Get Lead Categories By User ID
    public function getByUserId($user_id)
    {
        $categories = LeadCategory::where('user_id', $user_id)->get();

        return response()->json([
            'status' => true,
            'data' => $categories,
        ]);
    }
}
