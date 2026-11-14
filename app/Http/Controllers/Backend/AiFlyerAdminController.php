<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AiFlyerSetting;
use App\Models\FlyerGeneration;
use App\Models\Occasion;
use App\Services\Flyer\FlyerRetentionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AiFlyerAdminController extends Controller
{
    /**
     * Settings Page & Update
     */
    public function settings(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'ai_flyer_enabled' => 'required|in:0,1',
                'openai_model' => 'required|string|max:100',
                'image_quality' => 'required|in:standard,hd',
                'default_aspect_ratio' => 'required|in:1:1,4:5,9:16',
                'max_saved_flyers_per_user' => 'required|integer|min:1|max:100',
                'daily_generation_limit_per_user' => 'required|integer|min:1|max:100',
                'openai_api_key' => 'nullable|string',
            ]);

            foreach ($validated as $key => $val) {
                if ($key === 'openai_api_key' && empty($val)) {
                    continue; // do not overwrite with empty
                }
                AiFlyerSetting::set($key, (string) $val);
            }

            return redirect()->back()->with('success', 'AI Flyer settings updated successfully.');
        }

        $settings = [
            'ai_flyer_enabled' => AiFlyerSetting::get('ai_flyer_enabled', '1'),
            'openai_model' => AiFlyerSetting::get('openai_model', 'gpt-image-2.6-flare'),
            'image_quality' => AiFlyerSetting::get('image_quality', 'standard'),
            'default_aspect_ratio' => AiFlyerSetting::get('default_aspect_ratio', '1:1'),
            'max_saved_flyers_per_user' => AiFlyerSetting::get('max_saved_flyers_per_user', '10'),
            'daily_generation_limit_per_user' => AiFlyerSetting::get('daily_generation_limit_per_user', '5'),
            'openai_api_key' => AiFlyerSetting::get('openai_api_key', ''),
        ];

        return view('backend.ai_flyers.settings', compact('settings'));
    }

    /**
     * Occasions Calendar Management
     */
    public function occasions(Request $request)
    {
        $query = Occasion::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('name', 'like', "%$s%")
                  ->orWhere('region', 'like', "%$s%");
        }

        if ($request->filled('month')) {
            $m = str_pad($request->month, 2, '0', STR_PAD_LEFT);
            $query->where('date', 'like', "$m-%");
        }

        $occasions = $query->orderBy('date', 'asc')->paginate(20);

        return view('backend.ai_flyers.occasions', compact('occasions'));
    }

    public function storeOccasion(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:1000',
            'date' => 'required|string|regex:/^\d{2}-\d{2}$/',
            'year' => 'nullable|integer|min:2020|max:2099',
            'region' => 'required|string|max:100',
            'language' => 'required|in:en,hi,bilingual',
            'is_active' => 'required|boolean',
            'sample_prompts' => 'nullable|string|max:1000',
        ]);

        Occasion::create($validated);

        return redirect()->back()->with('success', 'Occasion created successfully.');
    }

    public function updateOccasion(Request $request, int $id)
    {
        $occasion = Occasion::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:1000',
            'date' => 'required|string|regex:/^\d{2}-\d{2}$/',
            'year' => 'nullable|integer|min:2020|max:2099',
            'region' => 'required|string|max:100',
            'language' => 'required|in:en,hi,bilingual',
            'is_active' => 'required|boolean',
            'sample_prompts' => 'nullable|string|max:1000',
        ]);

        $occasion->update($validated);

        return redirect()->back()->with('success', 'Occasion updated successfully.');
    }

    public function destroyOccasion(int $id)
    {
        $occasion = Occasion::findOrFail($id);
        $occasion->delete();

        return redirect()->back()->with('success', 'Occasion deleted successfully.');
    }

    /**
     * Usage Monitoring & Analytics
     */
    public function monitoring(Request $request)
    {
        $stats = [
            'total_generations' => FlyerGeneration::count(),
            'today_generations' => FlyerGeneration::whereDate('created_at', Carbon::today())->count(),
            'month_generations' => FlyerGeneration::whereMonth('created_at', Carbon::now()->month)->count(),
            'successful_generations' => FlyerGeneration::where('status', 'completed')->count(),
            'failed_generations' => FlyerGeneration::where('status', 'failed')->count(),
            'active_users' => FlyerGeneration::distinct('user_id')->count('user_id'),
        ];

        // Generation logs query
        $query = FlyerGeneration::with(['user', 'occasion'])->orderBy('id', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $generations = $query->paginate(15);

        return view('backend.ai_flyers.monitoring', compact('stats', 'generations'));
    }

    /**
     * Manual Retention Cleanup Trigger
     */
    public function triggerCleanup(FlyerRetentionService $retentionService)
    {
        $cleaned = $retentionService->cleanupAll();
        return redirect()->back()->with('success', "Retention cleanup executed successfully. Removed {$cleaned} old flyers.");
    }
}

