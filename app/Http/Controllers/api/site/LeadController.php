<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\Storage;

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

//    public function ocrExtract(Request $request)
//    {
//        $request->validate([
//            'images' => 'required|array|min:1|max:2',
//            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
//        ]);
   
//        $finalText = '';
   
//        foreach ($request->file('images') as $image) {
   
//            $path = $image->store('temp');
   
//            $fullPath = storage_path('app/' . $path);
   
//            $text = (new TesseractOCR($fullPath))
//                        ->lang('eng', 'hin')
//                        ->run();
   
//            $finalText .= "\n" . $text;
   
//            unlink($fullPath); // cleanup
//        }
   
//        return response()->json([
//            'success' => true,
//            'text' => trim($finalText)
//        ]);
//    }

public function ocrExtract(Request $request)
{
    // ✅ Temporary CORS headers
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    
    $request->validate([
        'images' => 'required|array|min:1|max:2',
        'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
    ]);

    $finalText = '';

    foreach ($request->file('images') as $image) {
        $path = $image->store('temp');
        $fullPath = storage_path('app/' . $path);

        $text = (new TesseractOCR($fullPath))
                    ->lang('eng', 'hin')
                    ->run();

        $finalText .= "\n" . $text;
        unlink($fullPath);
    }

    return response()->json([
        'success' => true,
        'text' => trim($finalText)
    ]);
}

}
