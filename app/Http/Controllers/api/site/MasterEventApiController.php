<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\Connection;
use App\Models\MasterDistrict;
use App\Models\MasterEvent;
use App\Models\MasterEventImage;
use App\Models\MasterState;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

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

        // ✅ Date filter (created_at)
        if (! empty($r->from_date) && ! empty($r->to_date)) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$r->from_date, $r->to_date]);
        } elseif (! empty($r->from_date)) {
            $query->whereDate('created_at', '>=', $r->from_date);
        } elseif (! empty($r->to_date)) {
            $query->whereDate('created_at', '<=', $r->to_date);
        }

        // ✅ IMPORTANT: Only show events where end_date >= current date (ONLY DATE compare)
        $query->whereDate('end_date', '>=', now()->format('Y-m-d'));

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
        if (! empty($r->search)) {
            $search = $r->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%$search%");
            });
        }

        // ✅ Sort
        // sort=latest OR sort=oldest
        $sort = $r->sort ?? 'latest';

        if ($sort == 'oldest') {
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

    public function getAllStates()
    {
        $states = MasterState::orderBy('state_name', 'ASC')->get();

        return response()->json([
            'status' => true,
            'message' => 'States fetched successfully',
            'data' => $states,
        ], 200);
    }

    public function getAllDistricts()
    {
        $districts = MasterDistrict::orderBy('district_name', 'ASC')->get();

        return response()->json([
            'status' => true,
            'message' => 'Districts fetched successfully',
            'data' => $districts,
        ], 200);
    }

    /**
     * Update an existing event
     */
    public function updateEvent(Request $request, $id)
    {
        try {
            $user = Auth::user();

            // Find event
            $event = MasterEvent::with('images')->find($id);

            if (! $event) {
                return response()->json([
                    'status' => false,
                    'message' => 'Event not found',
                ], 404);
            }

            // Check permission (only creator can update)
            if ($event->created_by != $user->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not authorized to update this event',
                ], 403);
            }

            // Validation rules
            $validator = Validator::make($request->all(), [
                'indus_type' => 'required|integer',
                'title' => 'required|string|max:100',
                'description' => 'nullable|string',
                'link' => 'nullable|url|max:255',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'timing' => 'nullable|date_format:H:i:s',
                'country_id' => 'nullable|integer',
                'state_id' => 'nullable|integer',
                'district_id' => 'nullable|integer',
                'location' => 'nullable|string|max:255',
                'event_type' => 'nullable|string|max:100',
                'max_attendees' => 'nullable|integer|min:1',
                'is_audience' => 'nullable|in:0,1',
                'is_online' => 'nullable|in:0,1',
                'status' => 'nullable|in:0,1',

                // Image validation
                'event_img' => 'nullable|array|max:5',
                'event_img.*' => 'file|mimes:jpg,jpeg,png,webp|max:5120', // 5MB max

                // Images to delete
                'delete_images' => 'nullable|array',
                'delete_images.*' => 'string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            DB::beginTransaction();

            // Handle image deletions
            if ($request->has('delete_images')) {
                $deleteImages = $request->input('delete_images');

                // If it's a single value, convert to array
                if (! is_array($deleteImages)) {
                    $deleteImages = [$deleteImages];
                }

                foreach ($deleteImages as $imagePath) {
                    // Find image record by path
                    $image = MasterEventImage::where('event_id', $id)
                        ->where('event_img_path', $imagePath)
                        ->first();

                    if ($image) {
                        // Delete physical file
                        $fullPath = public_path($image->event_img_path);
                        if (File::exists($fullPath)) {
                            File::delete($fullPath);
                        }

                        // Delete database record
                        $image->delete();
                    }
                }
            }

            // Handle new image uploads
            if ($request->hasFile('event_img')) {
                $this->uploadEventImages($request, $event);
            }

            // Update event data
            $updateData = [
                'indus_type' => $request->indus_type,
                'title' => $request->title,
                'description' => $request->description,
                'link' => $request->link,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'timing' => $request->timing,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'district_id' => $request->district_id,
                'location' => $request->location,
                'event_type' => $request->event_type,
                'max_attendees' => $request->max_attendees,
                'is_audience' => $request->is_audience,
                'is_online' => $request->is_online,
                'status' => $request->status,
            ];

            // Remove null values
            $updateData = array_filter($updateData, function ($value) {
                return $value !== null && $value !== '';
            });

            $event->update($updateData);

            DB::commit();

            // Load fresh data with relationships
            $updatedEvent = MasterEvent::with([
                'Industry',
                'country',
                'state',
                'district',
                'images',
                'createdByUser.masterUser',
            ])->find($id);

            return response()->json([
                'status' => true,
                'message' => 'Event updated successfully!',
                'data' => $updatedEvent,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Error updating event: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload images for event
     */
    private function uploadEventImages(Request $request, $event)
    {
        $files = $request->file('event_img');

        // Count existing images
        $existingImagesCount = MasterEventImage::where('event_id', $event->id)->count();

        // Check total images limit (5)
        if (($existingImagesCount + count($files)) > 5) {
            throw new \Exception('Maximum 5 images allowed per event');
        }

        // Create title-based folder name (sanitize title)
        $folderTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', $event->title);
        $folderPath = "assets/uploads/event/{$folderTitle}/";
        $fullPath = public_path($folderPath);

        // Create directory if not exists
        if (! file_exists($fullPath)) {
            mkdir($fullPath, 0777, true);
        }

        foreach ($files as $file) {
            // Generate unique filename
            $extension = $file->getClientOriginalExtension();
            $fileName = time().'_'.uniqid().'.'.$extension;

            // Move file
            $file->move($fullPath, $fileName);

            // Determine type (0 for image, 1 for video)
            $mimeType = $file->getMimeType();
            $type = str_contains($mimeType, 'video') ? 1 : 0;

            // Create database record
            MasterEventImage::create([
                'event_id' => $event->id,
                'event_img' => $fileName,
                'event_img_path' => $folderPath.$fileName,
                'type' => $type,
            ]);
        }
    }

    /**
     * Delete single image from event
     */
    public function deleteEventImage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'event_id' => 'required|integer',
                'image_path' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = Auth::user();
            $event = MasterEvent::find($request->event_id);

            if (! $event) {
                return response()->json([
                    'status' => false,
                    'message' => 'Event not found',
                ], 404);
            }

            // Check permission
            if ($event->created_by != $user->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Find and delete image
            $image = MasterEventImage::where('event_id', $request->event_id)
                ->where('event_img_path', $request->image_path)
                ->first();

            if ($image) {
                // Delete physical file
                $fullPath = public_path($image->event_img_path);
                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }

                // Delete database record
                $image->delete();

                // If folder is empty, delete it
                $folderPath = dirname($fullPath);
                if (is_dir($folderPath) && count(scandir($folderPath)) == 2) {
                    rmdir($folderPath);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Image deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error deleting image: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single event details
     */
    public function getEvent($id)
    {
        try {
            $event = MasterEvent::with([
                'Industry',
                'country',
                'state',
                'district',
                'images',
                'createdByUser.masterUser',
            ])->find($id);

            if (! $event) {
                return response()->json([
                    'status' => false,
                    'message' => 'Event not found',
                ], 404);
            }

            // Add created by user info
            $event->created_by_user_name = $event->createdByUser->user_name ?? null;
            $event->created_by_fname = $event->createdByUser->masterUser->fname ?? null;

            return response()->json([
                'status' => true,
                'message' => 'Event fetched successfully',
                'data' => $event,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching event: '.$e->getMessage(),
            ], 500);
        }
    }
}
