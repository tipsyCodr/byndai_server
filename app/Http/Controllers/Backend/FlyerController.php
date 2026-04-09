<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Flyer;
use App\Models\FlyerCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FlyerController extends Controller
{
    public function index(Request $request)
    {
        $category_id = $request->category_id;
        $category = FlyerCategory::findOrFail($category_id);
        $flyers = Flyer::where('category_id', $category_id)->get();
        
        return view('backend.flyers.index', compact('flyers', 'category'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:flyer_categories,id',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('flyers', 'public');
                
                Flyer::create([
                    'category_id' => $request->category_id,
                    'image_path' => 'storage/' . $path,
                    'is_active' => true,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Flyers uploaded successfully.');
    }

    public function destroy($id)
    {
        $flyer = Flyer::findOrFail($id);
        
        // Delete physical file
        $relativePath = str_replace('storage/', '', $flyer->image_path);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }

        $flyer->delete();
        return redirect()->back()->with('success', 'Flyer deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $flyer = Flyer::findOrFail($id);
        $flyer->is_active = !$flyer->is_active;
        $flyer->save();
        
        return redirect()->back()->with('success', 'Status updated.');
    }
}
