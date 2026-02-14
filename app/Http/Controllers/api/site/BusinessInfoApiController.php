<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\BusinessInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BusinessInfoApiController extends Controller
{
    // ✅ GET ALL (Admin type) OR User wise
    public function index(Request $r)
    {
        $user = Auth::user();

        // GET: All business info of logged-in user
        if ($r->isMethod('get')) {
            $data = BusinessInfo::where('user_id', $user->id)
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $data
            ], 200);
        }

        // POST: Create Business Info
        if ($r->isMethod('post')) {

            $validator = Validator::make($r->all(), [
                'legal_name' => 'required|string|max:150',
                'industry_id' => 'required|integer',
                'gst_no' => 'nullable|string|max:50',
                'address' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:500',
                'bank_acc_no' => 'nullable|string|max:50',
                'ifsc_code' => 'nullable|string|max:50',
                'branch_name' => 'nullable|string|max:100',
                'website_url' => 'nullable|string|max:255',
                'contact_email' => 'nullable|email|max:150',
                'upi_id' => 'nullable|string|max:100',
                'payment_qr' => 'nullable|string|max:255', // abhi string path
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            $payload = $validator->validated();
            $payload['user_id'] = $user->id;

            $created = BusinessInfo::create($payload);

            return response()->json([
                'status' => true,
                'message' => 'Business info created successfully',
                'data' => $created
            ], 201);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid request method'
        ], 405);
    }

    // ✅ GET by ID (Particular business info)
    public function show($id)
    {
        $user = Auth::user();

        $info = BusinessInfo::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$info) {
            return response()->json([
                'status' => false,
                'message' => 'Business info not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $info
        ], 200);
    }

    // ✅ GET by USER ID (Admin type feature)
    public function getByUserId($user_id)
    {
        $data = BusinessInfo::where('user_id', $user_id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    // ✅ UPDATE
    public function update(Request $r, $id)
    {
        $user = Auth::user();

        $info = BusinessInfo::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$info) {
            return response()->json([
                'status' => false,
                'message' => 'Business info not found'
            ], 404);
        }

        $validator = Validator::make($r->all(), [
            'legal_name' => 'required|string|max:150',
            'industry_id' => 'required|integer',
            'gst_no' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'bank_acc_no' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:50',
            'branch_name' => 'nullable|string|max:100',
            'website_url' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:150',
            'upi_id' => 'nullable|string|max:100',
            'payment_qr' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        $info->update($validator->validated());

        return response()->json([
            'status' => true,
            'message' => 'Business info updated successfully',
            'data' => $info
        ], 200);
    }

    // ✅ DELETE
    public function delete($id)
    {
        $user = Auth::user();

        $info = BusinessInfo::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$info) {
            return response()->json([
                'status' => false,
                'message' => 'Business info not found'
            ], 404);
        }

        $info->delete();

        return response()->json([
            'status' => true,
            'message' => 'Business info deleted successfully'
        ], 200);
    }
}
