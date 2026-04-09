<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\FlyerCategory;
use Illuminate\Http\Request;

class FlyerCategoryController extends Controller
{
    public function index()
    {
        $categories = FlyerCategory::withCount('flyers')->orderBy('date', 'asc')->get();
        return view('backend.flyers.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:festive,general',
            'date' => 'nullable|string', // Validated in frontend or as simple string
        ]);

        FlyerCategory::create($request->all());

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, $id)
    {
        $category = FlyerCategory::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:festive,general',
            'date' => 'nullable|string',
        ]);

        $category->update($request->all());

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        $category = FlyerCategory::findOrFail($id);
        $category->delete();
        return redirect()->back()->with('success', 'Category deleted successfully.');
    }
}
