<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    // ✅ Upload Images
    public function upload(Request $request)
    {
        $request->validate([
            'tag' => 'required|string',
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120'
        ]);

        // clean tag
        $tag = preg_replace('/[^A-Za-z0-9\-]/', '', $request->tag);

        $data = [];

        foreach ($request->file('images') as $index => $image) {

            $fileName = time().'_'.$index.'.'.$image->getClientOriginalExtension();

            // ✅ CORRECT STORAGE (public disk)
            $path = $image->storeAs($tag, $fileName, 'public');
            // ye store karega: storage/app/public/MakarSankranti/file.png

            // ✅ DB SAVE (ONLY RELATIVE PATH)
            $img = Image::create([
                'tag' => $tag,
                'file_name' => $fileName,
                'file_path' => $path // e.g. MakarSankranti/file.png
            ]);

            // ✅ RETURN FULL URL
            $data[] = asset('storage/'.$path);
        }

        return response()->json([
            'status' => true,
            'message' => 'Uploaded Successfully',
            'data' => $data
        ]);
    }

    // ✅ Get Images Grouped by Tag
    public function getImages()
    {
        $images = Image::all();

        $grouped = [];

        foreach ($images as $img) {
            $grouped[$img->tag][] = asset('storage/'.$img->file_path);
        }

        return response()->json($grouped);
    }

    // ✅ Delete Single Image
    public function deleteImage($id)
    {
        $image = Image::find($id);

        if (!$image) {
            return response()->json([
                'status' => false,
                'message' => 'Image not found'
            ], 404);
        }

        // ✅ DELETE FILE
        Storage::disk('public')->delete($image->file_path);

        // ✅ DELETE DB
        $image->delete();

        return response()->json([
            'status' => true,
            'message' => 'Image deleted successfully'
        ]);
    }

    // ✅ Delete Whole Tag Folder
    public function deleteTag($tag)
    {
        $tag = preg_replace('/[^A-Za-z0-9\-]/', '', $tag);

        // ✅ DELETE FOLDER
        Storage::disk('public')->deleteDirectory($tag);

        // ✅ DELETE DB RECORDS
        Image::where('tag', $tag)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Tag deleted successfully'
        ]);
    }
}