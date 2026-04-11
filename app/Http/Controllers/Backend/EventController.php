<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\MasterEvent;
use App\Models\MasterEventImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class EventController extends Controller
{
    /**
     * Display a listing of the events.
     */
    public function index(Request $request)
    {
        $query = MasterEvent::with(['Industry', 'country', 'state', 'district', 'createdByUser.masterUser']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $events = $query->latest()->paginate(20);
        return view('backend.events.index', compact('events'));
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy($id)
    {
        $event = MasterEvent::with('images')->findOrFail($id);

        // Delete all associated images physical files
        foreach ($event->images as $image) {
            $this->deleteMedia($image->event_img_path);
        }

        // Delete total folder if it exists in storage
        $folderTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', $event->title);
        $folderPath = "event/{$folderTitle}";
        if (Storage::disk('public')->exists($folderPath)) {
            Storage::disk('public')->deleteDirectory($folderPath);
        }

        // Delete image records from database
        MasterEventImage::where('event_id', $id)->delete();

        // Delete the event record
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event and associated data deleted successfully.');
    }

    /**
     * Toggle the status of the event.
     */
    public function toggleStatus($id)
    {
        $event = MasterEvent::findOrFail($id);
        $event->status = $event->status == 1 ? 0 : 1;
        $event->save();

        return redirect()->back()->with('success', 'Event status updated successfully.');
    }

    /**
     * Helper to delete media
     */
    private function deleteMedia($path)
    {
        if (!$path) {
            return;
        }

        if (str_starts_with($path, 'storage/')) {
            $relativePath = str_replace('storage/', '', $path);
            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }
        } else {
            $fullPath = public_path($path);
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }
    }
}
