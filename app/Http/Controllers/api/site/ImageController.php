<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    // ✅ Upload API
    public function upload(Request $request)
    {
        $request->validate([
            'tag' => 'required|string',
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp'
        ]);

        $tag = $request->tag;
        $paths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {

                // unique name
                $fileName = time().'_'.$index.'.'.$image->getClientOriginalExtension();

                // store in storage/app/public/{tag}
                $path = $image->storeAs("public/$tag", $fileName);

                // generate URL
                $paths[] = asset('storage/'.$tag.'/'.$fileName);
            }
        }

        return response()->json([
            'message' => 'Images uploaded successfully',
            'data' => $paths
        ]);
    }

    // ✅ Get API (Grouped by Tag)
    public function getImages()
    {
        $basePath = storage_path('app/public');
        $result = [];

        // get all folders (tags)
        $folders = array_filter(glob($basePath . '/*'), 'is_dir');

        foreach ($folders as $folder) {
            $tag = basename($folder);

            $files = glob($folder . '/*');

            $urls = [];

            foreach ($files as $file) {
                $fileName = basename($file);
                $urls[] = asset("storage/$tag/$fileName");
            }

            $result[$tag] = $urls;
        }

        return response()->json($result);
    }
}