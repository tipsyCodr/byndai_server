<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\CustomerMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerMasterApiController extends Controller
{
    public function index(Request $r)
    {
        $user_id = Auth::id();
        $data_provider = CustomerMaster::getData($r, $user_id);
        return response()->json(['status' => true, 'data' => $data_provider]);
    }
    public function create(Request $r)
    {
        $data = $r->validate([
            'full_name' => 'required|max:50',
            'company_name' => 'required|max:100',
            'designation' => 'nullable|string',
            'phone' => 'required|max:15',
            'website' => 'nullable|max:150',
            'email' => 'required|email|max:50',
            'address' => 'required|max:100',
            'date' => 'nullable|date',
            'front_image_url' => 'nullable|max:150',
            'back_image_url' => 'nullable|max:150',
            'cat_id' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $data['desg'] = $r->designation;
        $data['mobile_no'] = $r->phone;
        $data['user_id'] = Auth::id();
        $data['date'] = $r->date ?? now()->toDateString();
        $data['assign_to'] = Auth::id();
        $data['source'] = 1;
        $data['lead_status'] = 1;
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        $customer = CustomerMaster::create($data);
        return response()->json(['status' => true, 'message' => 'Customer created successfully', 'data' => $customer], 201);
    }

    public function update(Request $r, $id)
    {
        $data = $r->validate([
            'full_name' => 'required|max:50',
            'company_name' => 'required|max:100',
            'designation' => 'nullable|string',
            'phone' => 'required|max:15',
            'website' => 'nullable|max:150',
            'email' => 'required|email|max:50',
            'address' => 'required|max:100',
            'date' => 'nullable|date',
            'front_image_url' => 'nullable|max:150',
            'back_image_url' => 'nullable|max:150',
            'cat_id' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        if ($r->has('designation')) $data['desg'] = $r->designation;
        if ($r->has('phone')) $data['mobile_no'] = $r->phone;

        $customer = CustomerMaster::findOrFail($id);
        $customer->update($data);
        return response()->json(['status' => true, 'message' => 'Customer updated successfully', 'data' => $customer], 200);
    }
}
