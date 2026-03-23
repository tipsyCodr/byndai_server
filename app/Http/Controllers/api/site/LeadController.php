<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use thiagoalessio\TesseractOCR\TesseractOCR;

class LeadController extends Controller
{
    // ==========================
    // INDEX (Search + Filter + Pagination)
    // ==========================
    public function index(Request $request)
    {
        $query = Lead::query();

        // 🔍 SEARCH (name, company, email, mobiles)
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('company_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('email2', 'like', "%$search%")
                    ->orWhere('website', 'like', "%$search%")
                    ->orWhere('mobile_no_1', 'like', "%$search%")
                    ->orWhere('mobile_no_2', 'like', "%$search%")
                    ->orWhere('mobile_no_3', 'like', "%$search%");
            });
        }

        // 🏢 FILTER BY COMPANY
        if ($request->filled('company')) {
            $query->where('company_name', $request->company);
        }

        // 🏷️ FILTER BY CATEGORY
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // 👤 FILTER BY USER_ID
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // 📅 DATE FILTER
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [
                $request->from_date,
                $request->to_date,
            ]);
        }

        // 📄 PAGINATION
        $perPage = (int) $request->get('per_page', 10);
        $leads = $query->latest()->paginate($perPage);

        // Add URLs
        $leads->getCollection()->transform(function ($lead) {

            if ($lead->lead_image) {
                $lead->image_url = asset('storage/'.$lead->lead_image);
            }

            if ($lead->selfy_image_1) {
                $lead->selfy_image_1_url = asset('storage/'.$lead->selfy_image_1);
            }

            if ($lead->selfy_image_2) {
                $lead->selfy_image_2_url = asset('storage/'.$lead->selfy_image_2);
            }

            if ($lead->selfy_video) {
                $lead->selfy_video_url = asset('storage/'.$lead->selfy_video);
            }

            return $lead;
        });

        return response()->json([
            'success' => true,
            'data' => $leads->items(),
            'meta' => [
                'current_page' => $leads->currentPage(),
                'per_page' => $leads->perPage(),
                'total' => $leads->total(),
                'last_page' => $leads->lastPage(),
            ],
        ]);
    }

    // ==============================
    // STORE
    // =============================
    public function store(Request $request)
    { 
        $featureId = getFeatureId('leads_create');

        if (!$featureId) {
          return response()->json([
              'success' => false,
              'message' => 'Invalid feature configuration'
          ]);
        }

        $check = checkUserFeatureLimit($featureId);
        
        // ❌ Agar kuch error hai
        if (!$check['status']) {
            return response()->json([
                'success' => false,
                'message' => $check['message']
            ]);
        }

        // ❌ Limit exceed ho gaya
        if ($check['is_exceeded']) {
            return response()->json([
                'success' => false,
                'message' => 'Feature limit exceeded',
                'data' => $check
            ]);
        }

        $request->validate([
            'name' => 'required|string',

            'email' => 'required|email',
            'email2' => 'nullable|email',
            'website' => 'nullable|string',
            'company_name' => 'nullable|string',
            'address' => 'nullable|string',

            'category' => 'nullable|integer',
            // 'user_id' => 'required|integer',

            'lead_image' => 'nullable|string',
            'image_filename' => 'nullable|string',

            // mobiles
            'mobile_no_1' => 'required|string|max:15',
            'mobile_no_2' => 'nullable|string|max:15',
            'mobile_no_3' => 'nullable|string|max:15',

            // Selfy Images (base64)
            'selfy_image_1' => 'nullable|string',
            'selfy_image_2' => 'nullable|string',

            // Selfy Video (base64)
            'selfy_video' => 'nullable|string',
        ]);

        $user = auth()->user();

        $leadData = [
            'name' => $request->name,
            'company_name' => $request->company_name ?? '',
            'email' => $request->email,
            'email2' => $request->email2 ?? null,
            'website' => $request->website ?? null,
            'address' => $request->address ?? '',

            'category' => $request->category ?? null,
            // 'user_id' => $request->user_id,
            'user_id' => $user->id,

            'mobile_no_1' => $request->mobile_no_1,
            'mobile_no_2' => $request->mobile_no_2 ?? null,
            'mobile_no_3' => $request->mobile_no_3 ?? null,
        ];

        // =============================
        // Lead Image (Business Card Scan)
        // =============================
        if ($request->has('lead_image') && ! empty($request->lead_image)) {
            $imagePath = $this->saveBase64Image(
                $request->lead_image,
                $request->image_filename ?? 'scanned_image.jpg',
                'lead_images'
            );

            if ($imagePath) {
                $leadData['lead_image'] = $imagePath;
                $leadData['image_filename'] = $request->image_filename ?? 'scanned_image.jpg';
            }
        }

        // =============================
        // Selfy Images
        // =============================
        if ($request->filled('selfy_image_1')) {
            $path = $this->saveBase64Image(
                $request->selfy_image_1,
                'selfy_1.jpg',
                'lead_images'
            );

            if ($path) {
                $leadData['selfy_image_1'] = $path;
            }
        }

        if ($request->filled('selfy_image_2')) {
            $path = $this->saveBase64Image(
                $request->selfy_image_2,
                'selfy_2.jpg',
                'lead_images'
            );

            if ($path) {
                $leadData['selfy_image_2'] = $path;
            }
        }

        // =============================
        // Selfy Video (base64) with compress
        // =============================
        if ($request->filled('selfy_video')) {
            $videoPath = $this->saveBase64VideoAndCompress(
                $request->selfy_video,
                'selfy_video.mp4',
                'lead_images'
            );

            if ($videoPath) {
                $leadData['selfy_video'] = $videoPath;
            }
        }

        $lead = Lead::create($leadData);

        // return urls
        if ($lead->lead_image) {
            $lead->image_url = asset('storage/'.$lead->lead_image);
        }
        if ($lead->selfy_image_1) {
            $lead->selfy_image_1_url = asset('storage/'.$lead->selfy_image_1);
        }
        if ($lead->selfy_image_2) {
            $lead->selfy_image_2_url = asset('storage/'.$lead->selfy_image_2);
        }
        if ($lead->selfy_video) {
            $lead->selfy_video_url = asset('storage/'.$lead->selfy_video);
        }

        incrementFeatureUsage($featureId);

        return response()->json([
            'success' => true,
            'message' => 'Lead created successfully',
            'data' => $lead,
        ], 201);
    }

    // ==========================
    // SHOW
    // ==========================
    public function show($id)
    {
        $lead = Lead::findOrFail($id);

        if ($lead->lead_image) {
            $lead->image_url = asset('storage/'.$lead->lead_image);
        }
        if ($lead->selfy_image_1) {
            $lead->selfy_image_1_url = asset('storage/'.$lead->selfy_image_1);
        }
        if ($lead->selfy_image_2) {
            $lead->selfy_image_2_url = asset('storage/'.$lead->selfy_image_2);
        }
        if ($lead->selfy_video) {
            $lead->selfy_video_url = asset('storage/'.$lead->selfy_video);
        }

        return response()->json([
            'success' => true,
            'data' => $lead,
        ]);
    }

    // ==========================
    // UPDATE (with image support)
    // ==========================
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',

            'email' => 'nullable|email',
            'email2' => 'nullable|email',
            'website' => 'nullable|string',
            'company_name' => 'nullable|string',
            'address' => 'nullable|string',

            'category' => 'nullable|integer',
            'user_id' => 'nullable|integer',

            'mobile_no_1' => 'required|string|max:15',
            'mobile_no_2' => 'nullable|string|max:15',
            'mobile_no_3' => 'nullable|string|max:15',

            // Lead image
            'lead_image' => 'nullable|string',
            'image_filename' => 'nullable|string',

            // Selfy Images
            'selfy_image_1' => 'nullable|string',
            'selfy_image_2' => 'nullable|string',

            // Selfy Video
            'selfy_video' => 'nullable|string',
        ]);

        $lead = Lead::findOrFail($id);

        $updateData = [
            'name' => $request->name,
            'company_name' => $request->company_name,
            'email' => $request->email,
            'email2' => $request->email2,
            'website' => $request->website,
            'address' => $request->address,

            'category' => $request->category,
            'user_id' => $request->user_id ?? $lead->user_id,

            'mobile_no_1' => $request->mobile_no_1,
            'mobile_no_2' => $request->mobile_no_2,
            'mobile_no_3' => $request->mobile_no_3,
        ];

        // =============================
        // Lead Image update
        // =============================
        if ($request->filled('lead_image')) {
            if ($lead->lead_image) {
                $this->deleteStoredFile($lead->lead_image);
            }

            $imagePath = $this->saveBase64Image(
                $request->lead_image,
                $request->image_filename ?? 'updated_image.jpg',
                'lead_images'
            );

            if ($imagePath) {
                $updateData['lead_image'] = $imagePath;
                $updateData['image_filename'] = $request->image_filename ?? 'updated_image.jpg';
            }
        }

        // =============================
        // Selfy Image 1 update
        // =============================
        if ($request->filled('selfy_image_1')) {
            if ($lead->selfy_image_1) {
                $this->deleteStoredFile($lead->selfy_image_1);
            }

            $path = $this->saveBase64Image(
                $request->selfy_image_1,
                'selfy_1.jpg',
                'lead_images'
            );

            if ($path) {
                $updateData['selfy_image_1'] = $path;
            }
        }

        // =============================
        // Selfy Image 2 update
        // =============================
        if ($request->filled('selfy_image_2')) {
            if ($lead->selfy_image_2) {
                $this->deleteStoredFile($lead->selfy_image_2);
            }

            $path = $this->saveBase64Image(
                $request->selfy_image_2,
                'selfy_2.jpg',
                'lead_images'
            );

            if ($path) {
                $updateData['selfy_image_2'] = $path;
            }
        }

        // =============================
        // Selfy Video update (compress)
        // =============================
        if ($request->filled('selfy_video')) {
            if ($lead->selfy_video) {
                $this->deleteStoredFile($lead->selfy_video);
            }

            $videoPath = $this->saveBase64VideoAndCompress(
                $request->selfy_video,
                'selfy_video.mp4',
                'lead_images'
            );

            if ($videoPath) {
                $updateData['selfy_video'] = $videoPath;
            }
        }

        $lead->update($updateData);

        if ($lead->lead_image) {
            $lead->image_url = asset('storage/'.$lead->lead_image);
        }
        if ($lead->selfy_image_1) {
            $lead->selfy_image_1_url = asset('storage/'.$lead->selfy_image_1);
        }
        if ($lead->selfy_image_2) {
            $lead->selfy_image_2_url = asset('storage/'.$lead->selfy_image_2);
        }
        if ($lead->selfy_video) {
            $lead->selfy_video_url = asset('storage/'.$lead->selfy_video);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lead updated successfully',
            'data' => $lead,
        ]);
    }

    // ==========================
    // DELETE
    // ==========================
    public function destroy($id)
    {
        $lead = Lead::findOrFail($id);

        // delete images/videos
        if ($lead->lead_image) {
            $this->deleteStoredFile($lead->lead_image);
        }
        if ($lead->selfy_image_1) {
            $this->deleteStoredFile($lead->selfy_image_1);
        }
        if ($lead->selfy_image_2) {
            $this->deleteStoredFile($lead->selfy_image_2);
        }
        if ($lead->selfy_video) {
            $this->deleteStoredFile($lead->selfy_video);
        }

        $lead->delete();

        return response()->json(['success' => true]);
    }

    // ==========================
    // OCR EXTRACT (UNCHANGED)
    // ==========================
    public function ocrExtract(Request $request)
    {
        $request->validate([
            'images' => 'required|array|min:1|max:2',
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $finalText = '';
        $uploadPath = public_path('uploads/ocr');

        if (! file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        foreach ($request->file('images') as $image) {

            $filename = uniqid().'.'.$image->getClientOriginalExtension();
            $fullPath = $uploadPath.DIRECTORY_SEPARATOR.$filename;

            $image->move($uploadPath, $filename);

            try {
                $text = (new TesseractOCR($fullPath))
                    ->lang('eng')
                    ->run();

                $finalText .= "\n".$text;

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                ], 500);
            }
        }

        // 🔥 TEXT PARSING
        $jsonData = $this->parseOCRText($finalText);

        return response()->json([
            'success' => true,
            'text' => trim($finalText),
            'formdata' => $jsonData,
        ]);
    }

    
    public function getByUserId(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'category' => 'nullable|integer',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $perPage = $request->get('per_page', 10);
        $userId = $request->user_id;
        $category = $request->category;

        // Base Query
        $query = Lead::where('user_id', $userId);

        // ✅ Category Filter (only if selected)
        if (! empty($category)) {
            $query->where('category', $category);
        }

        // Latest + Pagination
        $leads = $query->latest()->paginate($perPage);

        // Add URLs
        $leads->getCollection()->transform(function ($lead) {
            if ($lead->lead_image) {
                $lead->image_url = asset('storage/'.$lead->lead_image);
            }
            if ($lead->selfy_image_1) {
                $lead->selfy_image_1_url = asset('storage/'.$lead->selfy_image_1);
            }
            if ($lead->selfy_image_2) {
                $lead->selfy_image_2_url = asset('storage/'.$lead->selfy_image_2);
            }
            if ($lead->selfy_video) {
                $lead->selfy_video_url = asset('storage/'.$lead->selfy_video);
            }

            return $lead;
        });

        return response()->json([
            'success' => true,
            'data' => $leads->items(),
            'meta' => [
                'current_page' => $leads->currentPage(),
                'per_page' => $leads->perPage(),
                'total' => $leads->total(),
                'last_page' => $leads->lastPage(),
                'next_page_url' => $leads->nextPageUrl(),
                'prev_page_url' => $leads->previousPageUrl(),
            ],
        ]);
    }

    // ==========================
    // HELPERS
    // ==========================

    // ✅ Save Base64 Image
    private function saveBase64Image($base64Image, $filename, $folder = 'lead_images')
    {
        try {
            if (strpos($base64Image, 'base64,') !== false) {
                $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            }

            $imageData = base64_decode($base64Image);

            if ($imageData === false) {
                Log::error('Failed to decode base64 image');

                return null;
            }

            $directory = storage_path("app/public/$folder");

            if (! file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            $uniqueFilename = time().'_'.uniqid().'_'.$filename;
            $filePath = $directory.'/'.$uniqueFilename;

            file_put_contents($filePath, $imageData);

            return "$folder/".$uniqueFilename;

        } catch (\Exception $e) {
            Log::error('Error saving base64 image: '.$e->getMessage());

            return null;
        }
    }

    // ✅ Save Base64 Video and Compress to <= 5MB
    private function saveBase64VideoAndCompress($base64Video, $filename, $folder = 'lead_images')
    {
        try {
            if (strpos($base64Video, 'base64,') !== false) {
                $base64Video = substr($base64Video, strpos($base64Video, ',') + 1);
            }

            $videoData = base64_decode($base64Video);

            if ($videoData === false) {
                Log::error('Failed to decode base64 video');

                return null;
            }

            $directory = storage_path("app/public/$folder");
            if (! file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            $uniqueFilename = time().'_'.uniqid().'_'.$filename;

            // original path
            $originalPath = $directory.'/ORIGINAL_'.$uniqueFilename;
            file_put_contents($originalPath, $videoData);

            // compressed path
            $compressedPath = $directory.'/'.$uniqueFilename;

            // if already <= 5MB
            if (filesize($originalPath) <= (5 * 1024 * 1024)) {
                rename($originalPath, $compressedPath);

                return "$folder/".$uniqueFilename;
            }

            // =============================
            // COMPRESS USING FFMPEG
            // =============================
            $cmd = 'ffmpeg -y -i '.escapeshellarg($originalPath).
                ' -t 10 -vf scale=640:-2 -r 24 -b:v 600k -b:a 64k '.
                escapeshellarg($compressedPath).' 2>&1';

            exec($cmd, $output, $status);

            if ($status !== 0) {
                Log::error('FFMPEG compression failed: '.implode("\n", $output));

                // fallback original
                rename($originalPath, $compressedPath);

                return "$folder/".$uniqueFilename;
            }

            // remove original
            if (file_exists($originalPath)) {
                unlink($originalPath);
            }

            // final size check (still > 5MB then keep compressed anyway)
            return "$folder/".$uniqueFilename;

        } catch (\Exception $e) {
            Log::error('Error saving base64 video: '.$e->getMessage());

            return null;
        }
    }

    // ✅ Delete stored file safely
    private function deleteStoredFile($relativePath)
    {
        try {
            $fullPath = storage_path('app/public/'.$relativePath);

            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        } catch (\Exception $e) {
            Log::error('File delete error: '.$e->getMessage());
        }
    }

    // ==========================
    // OCR PARSER (UNCHANGED)
    // ==========================
    private function parseOCRText($text)
    {
        $lines = preg_split('/\r\n|\r|\n/', $text);

        $data = [
            'name' => '',
            'company' => '',
            'mobile1' => '',
            'mobile2' => '',
            'mobile3' => '',
            'email1' => '',
            'email2' => '',
            'website' => '',
            'address' => '',
        ];

        $mobiles = [];
        $emails = [];
        $addresses = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (! $line) {
                continue;
            }

            // 📧 Email
            if (preg_match('/[\w\.-]+@[\w\.-]+\.\w+/', $line, $m)) {
                $emails[] = $m[0];

                continue;
            }

            // 📱 Phone
            if (preg_match_all('/\+?\d[\d\s\-]{8,14}\d/', $line, $m)) {
                foreach ($m[0] as $phone) {
                    $phone = preg_replace('/\D/', '', $phone);
                    if (strlen($phone) >= 10) {
                        $mobiles[] = $phone;
                    }
                }

                continue;
            }

            // 🌐 Website
            if (! $data['website'] && preg_match('/(www\.)?[a-z0-9\-]+\.(com|in|io|net|org)/i', $line, $m)) {
                $data['website'] = $m[0];

                continue;
            }

            // 🏢 Company
            if (! $data['company'] && $this->isCompanyName($line)) {
                $data['company'] = $line;

                continue;
            }

            // 👤 Name
            if (! $data['name'] && $this->isPersonName($line)) {
                $data['name'] = $line;

                continue;
            }

            // 🏠 Address
            if ($this->isAddress($line)) {
                $addresses[] = $line;
            }
        }

        // Fill phones
        $mobiles = array_values(array_unique($mobiles));
        $data['mobile1'] = $mobiles[0] ?? '';
        $data['mobile2'] = $mobiles[1] ?? '';
        $data['mobile3'] = $mobiles[2] ?? '';

        // Fill emails
        $emails = array_values(array_unique($emails));
        $data['email1'] = $emails[0] ?? '';
        $data['email2'] = $emails[1] ?? '';

        // Merge address
        $data['address'] = implode(', ', array_unique($addresses));

        return $data;
    }

    private function isCompanyName($text)
    {
        $keywords = [
            'ltd', 'private', 'pvt', 'inc', 'llp', 'enterprise', 'enterprises',
            'industries', 'corp', 'corporation', 'solutions', 'group',
            'traders', 'exports', 'company', 'tech', 'systems', 'services',
        ];

        $textLower = strtolower($text);

        foreach ($keywords as $k) {
            if (str_contains($textLower, $k)) {
                return true;
            }
        }

        if (strtoupper($text) === $text && str_word_count($text) > 1) {
            return true;
        }

        return false;
    }

    private function isPersonName($text)
    {
        $words = explode(' ', $text);
        if (count($words) > 3) {
            return false;
        }

        foreach ($words as $w) {
            if (! preg_match('/^[A-Z][a-z]+$/', $w)) {
                return false;
            }
        }

        return true;
    }

    private function isAddress($text)
    {
        $keywords = [
            'street', 'road', 'rd', 'sector', 'block', 'lane', 'ln', 'colony',
            'district', 'near', 'area', 'city', 'state', 'country', 'pin', 'po',
        ];

        $lower = strtolower($text);

        foreach ($keywords as $k) {
            if (str_contains($lower, $k)) {
                return true;
            }
        }

        return preg_match('/\d+/', $text) && preg_match('/[a-zA-Z]/', $text);
    }

    public function helperFunctionTest(Request $request)
{
    $featureId = $request->feature_id; // ya manually bhi de sakte ho

    $result = checkUserFeatureLimit($featureId);

    // ❌ Agar kuch error hai
    if (!$result['status']) {
        return response()->json([
            'success' => false,
            'message' => $result['message']
        ]);
    }

    // ❌ Limit exceed ho gaya
    if ($result['is_exceeded']) {
        return response()->json([
            'success' => false,
            'message' => 'Feature limit exceeded',
            'data' => $result
        ]);
    }

    // ✅ Allowed
    return response()->json([
        'success' => true,
        'message' => 'Feature allowed',
        'data' => $result
    ]);
}
}
