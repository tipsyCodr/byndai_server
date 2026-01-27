<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use OpenAI;

class LeadController extends Controller
{
   

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
    
        // 📅 DATE FILTER
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [
                $request->from_date,
                $request->to_date
            ]);
        }
    
        // 📄 PAGINATION
        $perPage = $request->get('per_page', 10);
    
        $leads = $query->latest()->paginate($perPage);
    
        return response()->json([
            'success' => true,
            'data' => $leads->items(),
            'meta' => [
                'current_page' => $leads->currentPage(),
            'per_page'     => $leads->perPage(),
            'total'        => $leads->total(),
            'last_page'    => $leads->lastPage(),
             ]
        ]);
    }


    // ✅ STORE lead
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'mobiles' => 'required|array|min:1|max:3',
            'mobiles.*' => 'required|string|max:15',
            'email2' => 'nullable|email', 
            'website' => 'nullable|string',
        ]);

        $mobiles = $request->mobiles;

        $lead = Lead::create([
            'name' => $request->name,
            'company_name' => $request->company_name,
            'email' => $request->email,
            'email2' => $request->email2,   
            'website' => $request->website,
            'address' => $request->address,
            'mobile_no_1' => $mobiles[0],
            'mobile_no_2' => $mobiles[1] ?? null,
            'mobile_no_3' => $mobiles[2] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lead created successfully',
            'data' => $lead
        ], 201);
    }

    // ✅ SHOW single lead
    public function show($id)
    {
        return Lead::findOrFail($id);
    }

    // ✅ DELETE
    public function destroy($id)
    {
        Lead::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    // ✅ UPDATE lead
   public function update(Request $request, $id)
   {
       $request->validate([
           'name' => 'required|string',
           'mobile_no_1' => 'required|string|max:15',
           'mobile_no_2' => 'nullable|string|max:15',
           'mobile_no_3' => 'nullable|string|max:15',
           'email' => 'nullable|email',
           'email2' => 'nullable|email',   
           'website' => 'nullable|string',
           'company_name' => 'nullable|string',
           'address' => 'nullable|string',
       ]);
   
       $lead = Lead::findOrFail($id);
   
       $lead->update([
           'name' => $request->name,
           'company_name' => $request->company_name,
           'email' => $request->email,
           'email2' => $request->email2,   
           'website' => $request->website, 
           'address' => $request->address,
           'mobile_no_1' => $request->mobile_no_1,
           'mobile_no_2' => $request->mobile_no_2,
           'mobile_no_3' => $request->mobile_no_3,
       ]);
   
       return response()->json([
           'success' => true,
           'message' => 'Lead updated successfully',
           'data' => $lead
       ]);
   }






//      public function ocrExtract(Request $request)
//    {
//        $request->validate([
//            'images' => 'required|array|min:1|max:2',
//            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
//        ]);

//        $finalText = '';

//        $uploadPath = public_path('uploads/ocr');

//        if (!file_exists($uploadPath)) {
//            mkdir($uploadPath, 0777, true);
//        }

//        foreach ($request->file('images') as $image) {

//            // 🔹 Save image in public/uploads/ocr
//            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
//            $fullPath = $uploadPath . DIRECTORY_SEPARATOR . $filename;

//            $image->move($uploadPath, $filename);

//            // 🔹 Log image path (console log)
//            Log::info("OCR Image Received:", [
//                'file' => $filename,
//                'path' => $fullPath
//            ]);

//            // 🔹 Run OCR
//            try {
//                $text = (new TesseractOCR($fullPath))
//                            ->lang('eng')
//                            ->run();

//                // 🔹 Log OCR Output
//                Log::info("OCR Output:", [
//                    'text' => $text
//                ]);

//                $finalText .= "\n" . $text;

//            } catch (\Exception $e) {

//                Log::error("OCR Failed:", [
//                    'error' => $e->getMessage()
//                ]);

//                return response()->json([
//                    'success' => false,
//                    'error' => $e->getMessage()
//                ], 500);
//            }
//        }

//        return response()->json([
//            'success' => true,
//            'text' => trim($finalText)
//        ]);
//      }

public function ocrExtract(Request $request)
{
    $request->validate([
        'images' => 'required|array|min:1|max:2',
        'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
    ]);

    $finalText = '';
    $uploadPath = public_path('uploads/ocr');

    if (!file_exists($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    foreach ($request->file('images') as $image) {

        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
        $fullPath = $uploadPath . DIRECTORY_SEPARATOR . $filename;

        $image->move($uploadPath, $filename);

        try {
            $text = (new TesseractOCR($fullPath))
                        ->lang('eng')
                        ->run();

            $finalText .= "\n" . $text;

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 🔥 TEXT PARSING
    $jsonData = $this->parseOCRText($finalText);

    return response()->json([
        'success' => true,
        'text' => trim($finalText),
        'formdata' => $jsonData
    ]);
}


private function parseOCRText($text)
{
    $lines = preg_split('/\r\n|\r|\n/', $text);

    $data = [
        "name" => "",
        "company" => "",
        "mobile1" => "",
        "mobile2" => "",
        "mobile3" => "",
        "email1" => "",
        "email2" => "",
        "website" => "",
        "address" => ""
    ];

    $mobiles = [];
    $emails = [];
    $addresses = [];

    foreach ($lines as $line) {
        $line = trim($line);
        if (!$line) continue;

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
        if (!$data['website'] && preg_match('/(www\.)?[a-z0-9\-]+\.(com|in|io|net|org)/i', $line, $m)) {
            $data['website'] = $m[0];
            continue;
        }

        // 🏢 Company
        if (!$data['company'] && $this->isCompanyName($line)) {
            $data['company'] = $line;
            continue;
        }

        // 👤 Name
        if (!$data['name'] && $this->isPersonName($line)) {
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
        'ltd','private','pvt','inc','llp','enterprise','enterprises',
        'industries','corp','corporation','solutions','group',
        'traders','exports','company','tech','systems','services'
    ];

    $textLower = strtolower($text);

    foreach ($keywords as $k) {
        if (str_contains($textLower, $k)) return true;
    }

    if (strtoupper($text) === $text && str_word_count($text) > 1) return true;

    return false;
}

private function isPersonName($text)
{
    $words = explode(' ', $text);
    if (count($words) > 3) return false;

    foreach ($words as $w) {
        if (!preg_match('/^[A-Z][a-z]+$/', $w)) return false;
    }
    return true;
}

private function isAddress($text)
{
    $keywords = [
        'street','road','rd','sector','block','lane','ln','colony',
        'district','near','area','city','state','country','pin','po'
    ];

    $lower = strtolower($text);

    foreach ($keywords as $k) {
        if (str_contains($lower, $k)) return true;
    }

    return preg_match('/\d+/', $text) && preg_match('/[a-zA-Z]/', $text);
}




}
