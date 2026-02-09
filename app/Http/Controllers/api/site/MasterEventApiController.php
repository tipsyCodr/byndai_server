<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\Connection;
use App\Models\MasterEvent;
use App\Models\MasterEventImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MasterEventApiController extends Controller
{
  

    public function showEvent(Request $r)
    {
        $user_id = Auth::id();

        $query = MasterEvent::with([
            'Industry',
            'country',
            'state',
            'district',
            'images',
            'createdByUser.masterUser',
        ]);

        // ✅ Audience filter
        if ($r->is_audience == 1) {

            $connections = Connection::where('user_id', $user_id)
                ->pluck('friend_id')
                ->toArray();

            $query->where(function ($q) use ($connections, $user_id) {
                $q->whereIn('created_by', $connections)
                    ->orWhere('created_by', $user_id);
            });

        } else {
            $query->where('is_audience', 0);
        }

        // ✅ Search
        if (! empty($r->search)) {
            $search = $r->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%$search%");
            });
        }

        // ✅ Filters
        if (! empty($r->industry_type)) {
            $query->where('indus_type', $r->industry_type);
        }

        if (! empty($r->country_id)) {
            $query->where('country_id', $r->country_id);
        }

        if (! empty($r->state_id)) {
            $query->where('state_id', $r->state_id);
        }

        if (! empty($r->district_id)) {
            $query->where('district_id', $r->district_id);
        }

        // ✅ Date filter
        if (! empty($r->from_date) && ! empty($r->to_date)) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$r->from_date, $r->to_date]);
        } elseif (! empty($r->from_date)) {
            $query->whereDate('created_at', '>=', $r->from_date);
        } elseif (! empty($r->to_date)) {
            $query->whereDate('created_at', '<=', $r->to_date);
        }

        // ✅ Latest first
        $query->orderBy('id', 'DESC');

        // ✅ Pagination
        $perPage = 10;
        $data_provider = $query->paginate($perPage);

        // ✅ Add created_by user info inside each event
        $data_provider->getCollection()->transform(function ($event) {

            $event->created_by_user_name = $event->createdByUser->user_name ?? null;
            $event->created_by_fname = $event->createdByUser->masterUser->fname ?? null;

            return $event;
        });

        return response()->json([
            'status' => true,
            'message' => 'Event list fetched successfully',
            'total_events' => $data_provider->total(),
            'current_page' => $data_provider->currentPage(),
            'per_page' => $data_provider->perPage(),
            'last_page' => $data_provider->lastPage(),
            'next_page_url' => $data_provider->nextPageUrl(),
            'prev_page_url' => $data_provider->previousPageUrl(),
            'data' => $data_provider->items(),
        ]);
    }

    public function eventImage(Request $r, $title, $event_id)
    {
        if ($r->hasFile('event_img')) {

            $files = $r->file('event_img');

            foreach ($files as $file) {

                // ✅ mimeType pehle
                $mimeType = $file->getMimeType();
                $type = str_contains($mimeType, 'video') ? 1 : 0;

                $fileName = time().'_'.uniqid().'_'.$file->getClientOriginalName();

                $folderPath = "assets/uploads/event/$title/";
                $fullPath = public_path($folderPath);

                if (! file_exists($fullPath)) {
                    mkdir($fullPath, 0777, true);
                }

                // ✅ ab move
                $file->move($fullPath, $fileName);

                MasterEventImage::create([
                    'event_id' => $event_id,
                    'event_img' => $fileName,
                    'event_img_path' => $folderPath.$fileName,
                    'type' => $type,
                ]);
            }
        }
    }

    public function createEvent(Request $r)
    {
        $user = Auth::user();

        $data = $r->validate([
            'indus_type' => 'required',
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'link' => 'nullable|url|max:255',

            // ✅ multiple max 5
            'event_img' => 'nullable|array|max:5',
            'event_img.*' => 'file|mimes:jpg,jpeg,png,webp,mp4,mov,avi|max:10240',

            'start_date' => 'required',
            'end_date' => 'nullable|after_or_equal:start_date',
            'timing' => 'nullable|date_format:H:i:s',
            'country_id' => 'nullable',
            'state_id' => 'nullable',
            'district_id' => 'nullable',
            'location' => 'nullable|string|max:255',
            'event_type' => 'nullable|string|max:100',
            'max_attendees' => 'nullable|integer|min:1',
            'is_audience' => 'nullable',
            'is_online' => 'nullable',
            'status' => 'nullable|integer|in:0,1',
        ]);

        $data['created_by'] = $user->id;

        $event = MasterEvent::create($data);

        if ($r->hasFile('event_img')) {
            $this->eventImage($r, $r->title, $event->id);
        }

        return response()->json([
            'status' => true,
            'message' => 'Event created successfully!',
            'event_id' => $event->id,
        ]);
    }

    public function deleteEevent($eid)
    {
        $event = MasterEvent::with('images')->findOrFail($eid);

        // ✅ Folder path (title ke naam ka)
        $folderPath = public_path('assets/uploads/event/'.$event->title);

        // ✅ Delete all images rows
        MasterEventImage::where('event_id', $eid)->delete();

        // ✅ Delete Event
        $event->delete();

        // ✅ Delete folder (title wala)
        if (File::exists($folderPath)) {
            File::deleteDirectory($folderPath);
        }

        return response()->json([
            'status' => true,
            'message' => 'Event deleted successfully!',
        ]);
    }

    public function getEventsByUserId(Request $r, $user_id)
    {
    $query = MasterEvent::with([
        'Industry',
        'country',
        'state',
        'district',
        'images',
        'createdByUser.masterUser',
    ])->where('created_by', $user_id);

    // ✅ Search (title + description)
    if (!empty($r->search)) {
        $search = $r->search;

        $query->where(function ($q) use ($search) {
            $q->where('title', 'LIKE', "%$search%")
              ->orWhere('description', 'LIKE', "%$search%");
        });
    }

    // ✅ Sort
    // sort=latest OR sort=oldest
    $sort = $r->sort ?? "latest";

    if ($sort == "oldest") {
        $query->orderBy('id', 'ASC');
    } else {
        $query->orderBy('id', 'DESC'); // latest default
    }

    // ✅ Pagination
    $perPage = $r->per_page ?? 10;
    $data_provider = $query->paginate($perPage);

    // ✅ extra created_by info
    $data_provider->getCollection()->transform(function ($event) {
        $event->created_by_user_name = $event->createdByUser->user_name ?? null;
        $event->created_by_fname = $event->createdByUser->masterUser->fname ?? null;
        return $event;
    });

    return response()->json([
        'status' => true,
        'message' => 'User events fetched successfully',
        'total_events' => $data_provider->total(),
        'current_page' => $data_provider->currentPage(),
        'per_page' => $data_provider->perPage(),
        'last_page' => $data_provider->lastPage(),
        'next_page_url' => $data_provider->nextPageUrl(),
        'prev_page_url' => $data_provider->previousPageUrl(),
        'data' => $data_provider->items(),
    ]);
    }

}
