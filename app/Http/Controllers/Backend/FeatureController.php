<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $features = Feature::orderBy('feature')->get();
        return view('backend.features.index', compact('features'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'feature' => 'required|string|max:100|unique:features,feature',
        ]);

        Feature::create($request->all());

        return redirect()->route('admin.features.index')
            ->with('success', 'Feature created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $feature = Feature::findOrFail($id);

        $request->validate([
            'feature' => 'required|string|max:100|unique:features,feature,' . $id,
        ]);

        $feature->update($request->all());

        return redirect()->route('admin.features.index')
            ->with('success', 'Feature updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $feature = Feature::findOrFail($id);
        
        // Prevent deletion if linked to plan features?
        if ($feature->planFeatures()->count() > 0) {
            return redirect()->route('admin.features.index')
                ->with('error', 'Cannot delete feature as it is currently assigned to one or more plans.');
        }

        $feature->delete();

        return redirect()->route('admin.features.index')
            ->with('success', 'Feature deleted successfully.');
    }
}
