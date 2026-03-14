<?php

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
    // ... aapke existing methods ...

    /**
     * Update an existing event
     */
    public function updateEvent(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            // Find event
            $event = MasterEvent::with('images')->find($id);
            
            if (!$event) {
                return response()->json([
                    'status' => false,
                    'message' => 'Event not found'
                ], 404);
            }

            // Check permission (only creator can update)
            if ($event->created_by != $user->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not authorized to update this event'
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
                'delete_images.*' => 'string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Handle image deletions
            if ($request->has('delete_images')) {
                $deleteImages = $request->input('delete_images');
                
                // If it's a single value, convert to array
                if (!is_array($deleteImages)) {
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
            $updateData = array_filter($updateData, function($value) {
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
                'createdByUser.masterUser'
            ])->find($id);

            return response()->json([
                'status' => true,
                'message' => 'Event updated successfully!',
                'data' => $updatedEvent
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => false,
                'message' => 'Error updating event: ' . $e->getMessage()
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
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0777, true);
        }

        foreach ($files as $file) {
            // Generate unique filename
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . uniqid() . '.' . $extension;
            
            // Move file
            $file->move($fullPath, $fileName);
            
            // Determine type (0 for image, 1 for video)
            $mimeType = $file->getMimeType();
            $type = str_contains($mimeType, 'video') ? 1 : 0;
            
            // Create database record
            MasterEventImage::create([
                'event_id' => $event->id,
                'event_img' => $fileName,
                'event_img_path' => $folderPath . $fileName,
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
                'image_path' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $event = MasterEvent::find($request->event_id);

            if (!$event) {
                return response()->json([
                    'status' => false,
                    'message' => 'Event not found'
                ], 404);
            }

            // Check permission
            if ($event->created_by != $user->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
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
                'message' => 'Image deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error deleting image: ' . $e->getMessage()
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
                'createdByUser.masterUser'
            ])->find($id);

            if (!$event) {
                return response()->json([
                    'status' => false,
                    'message' => 'Event not found'
                ], 404);
            }

            // Add created by user info
            $event->created_by_user_name = $event->createdByUser->user_name ?? null;
            $event->created_by_fname = $event->createdByUser->masterUser->fname ?? null;

            return response()->json([
                'status' => true,
                'message' => 'Event fetched successfully',
                'data' => $event
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching event: ' . $e->getMessage()
            ], 500);
        }
    }
}