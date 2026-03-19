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
            
            if (is_string($deleteImages)) {
                $deleteImages = [$deleteImages];
            }

            foreach ($deleteImages as $imagePath) {
                $image = MasterEventImage::where('event_id', $id)
                    ->where('event_img_path', $imagePath)
                    ->first();

                if ($image) {
                    $fullPath = public_path($image->event_img_path);
                    if (File::exists($fullPath)) {
                        File::delete($fullPath);
                    }
                    $image->delete();
                }
            }
        }

        // Handle new image uploads - IMPROVED VERSION
        if ($request->hasFile('event_img')) {
            $files = $request->file('event_img');
            
            // Debug: Check if files are valid
            foreach ($files as $key => $file) {
                if (!$file->isValid()) {
                    \Log::error("File {$key} is invalid: " . $file->getErrorMessage());
                    throw new \Exception("Invalid file upload: " . $file->getErrorMessage());
                }
            }

            $existingCount = MasterEventImage::where('event_id', $id)->count();
            if (($existingCount + count($files)) > 5) {
                throw new \Exception('Maximum 5 images allowed per event');
            }

            $this->uploadEventImages($request, $event);
        }

        // Update event data
        $updateData = array_filter([
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
        ], function ($value) {
            return $value !== null && $value !== '';
        });

        $event->update($updateData);

        DB::commit();

        // Load fresh data
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
        
        \Log::error('Event Update Error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());

        return response()->json([
            'status' => false,
            'message' => 'Error updating event: ' . $e->getMessage(),
        ], 500);
    }
}

/**
 * Upload images for event - IMPROVED VERSION
 */
private function uploadEventImages(Request $request, $event)
{
    $files = $request->file('event_img');
    
    if (empty($files)) {
        return;
    }

    // Ensure files is an array
    if (!is_array($files)) {
        $files = [$files];
    }

    $existingCount = MasterEventImage::where('event_id', $event->id)->count();
    
    if (($existingCount + count($files)) > 5) {
        throw new \Exception('Maximum 5 images allowed per event');
    }

    // Sanitize title for folder name
    $folderTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', $event->title);
    $folderPath = "assets/uploads/event/{$folderTitle}/";
    $fullPath = public_path($folderPath);

    if (!file_exists($fullPath)) {
        mkdir($fullPath, 0777, true);
    }

    foreach ($files as $index => $file) {
        try {
            // Double-check file validity
            if (!$file->isValid()) {
                \Log::warning("Skipping invalid file at index {$index}: " . $file->getErrorMessage());
                continue;
            }

            // Get file contents directly
            $fileContent = file_get_contents($file->getRealPath());
            if ($fileContent === false) {
                throw new \Exception("Could not read file contents");
            }

            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . uniqid() . '_' . $index . '.' . $extension;
            
            // Write file manually
            $writeSuccess = file_put_contents($fullPath . $fileName, $fileContent);
            
            if ($writeSuccess === false) {
                throw new \Exception("Failed to write file to destination");
            }

            $mimeType = $file->getMimeType();
            $type = str_contains($mimeType, 'video') ? 1 : 0;

            MasterEventImage::create([
                'event_id' => $event->id,
                'event_img' => $fileName,
                'event_img_path' => $folderPath . $fileName,
                'type' => $type,
            ]);

            \Log::info("Successfully uploaded image: {$fileName}");

        } catch (\Exception $e) {
            \Log::error("Error uploading file at index {$index}: " . $e->getMessage());
            throw $e;
        }
    }
}