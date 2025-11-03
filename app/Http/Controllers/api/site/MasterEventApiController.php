<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\MasterEvent;
use App\Models\MasterEventImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterEventApiController extends Controller
{
    public function showEvent(Request $r)
    {
        $data_provider = MasterEvent::getData($r);
        return response()->json(['status' => true, 'data' => $data_provider]);
    }
    public function eventImage(Request $r, $title, $event_id)
    {
        if ($r->hasFile('event_img')) {
            $files = $r->file('event_img');
            if (!is_array($files)) {
                $files = [$files];
            }
            foreach ($files as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = public_path('assets/uploads/event/' . $title . '/');
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $file->move($path, $fileName);
                $mimeType = $file->getMimeType();
                $type = str_contains($mimeType, 'video') ? 1 : 0;
                MasterEventImage::create(['event_id' => $event_id,'event_img' => $fileName,'type' => $type]);
            }
        }
    }
    public function createEvent(Request $r)
    {
        $user = Auth::user();
        $data = $r->validate(['indus_type' => 'required', 'title' => 'required|string|max:100', 'description' => 'nullable|string', 'link' => 'nullable|url|max:255', 'event_img' => 'nullable', 'start_date' => 'required|date', 'end_date' => 'nullable|date|after_or_equal:start_date', 'timing' => 'nullable|date_format:H:i', 'country_id' => 'nullable', 'state_id' => 'nullable', 'district_id' => 'nullable', 'location' => 'nullable|string|max:255', 'event_type' => 'nullable|string|max:100', 'max_attendees' => 'nullable|integer|min:1', 'is_audience' => 'nullable', 'is_online' => 'nullable', 'status' => 'nullable|integer|in:0,1', 'created_by' => 'nullable|exists:users,id']);
        $data['created_by'] = $user->id;
        $event = MasterEvent::create($data);
        $event_id = $event->id;
        if ($r->hasFile('event_img')) {
            $this->eventImage($r, $r->title, $event_id);
        }
        return response()->json(['message' => 'Event created successfully!', 'event_id' => $event_id]);
    }
    public function deleteEevent($eid)
    {
        $event = MasterEvent::findOrFail($eid);
        MasterEventImage::where('event_id', $eid)->delete();
        $event->delete();
        return response()->json(['message' => 'Event deleted successfully!']);
    }
}
