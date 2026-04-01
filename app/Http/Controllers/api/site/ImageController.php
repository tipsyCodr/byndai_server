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
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp'
        ]);

        $tag = preg_replace('/[^A-Za-z0-9\-]/', '', $request->tag);
        $data = [];

        foreach ($request->file('images') as $index => $image) {

            $fileName = time().'_'.$index.'.'.$image->getClientOriginalExtension();

            // store in storage
            $path = $image->storeAs("public/$tag", $fileName);

            // save in DB
            $img = Image::create([
                'tag' => $tag,
                'file_name' => $fileName,
                'file_path' => "storage/$tag/$fileName"
            ]);

            $data[] = asset($img->file_path);
        }

        return response()->json([
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
            $grouped[$img->tag][] = asset($img->file_path);
        }

        return response()->json($grouped);
    }

    // ✅ Delete Single Image
    public function deleteImage($id)
    {
        $image = Image::find($id);

        if (!$image) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        // delete file
        Storage::delete('public/' . $image->tag . '/' . $image->file_name);

        // delete DB
        $image->delete();

        return response()->json(['message' => 'Image deleted']);
    }

    // ✅ Delete Whole Tag Folder
    public function deleteTag($tag)
    {
        $tag = preg_replace('/[^A-Za-z0-9\-]/', '', $tag);

        // delete folder
        Storage::deleteDirectory("public/$tag");

        // delete DB records
        Image::where('tag', $tag)->delete();

        return response()->json(['message' => 'Tag deleted']);
    }
}