<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterUserDetail;
use App\Models\MasterLink;
use App\Models\MasterKeyword;
use App\Models\BusinessInfo;
use App\Models\Product;

class ProfileMilestoneController extends Controller
{
    public function profileCompletion(Request $request)
    {
        $userId = $request->user()->id;

        $totalMilestones = 5;
        $completed = 0;

        /*
        ==============================
        1️⃣ USER DETAILS CHECK
        ==============================
        */
        $details = MasterUserDetail::where('user_id', $userId)->first();

        $userDetailsComplete = false;

        if ($details) {

            $requiredFields = [
                'profile_pic',
                'profile_video',
                'profile_logo',
                'profile_banner',
                'fname',
                'dob',
                'gender',
                'email_one',
                'mobile_one',
                'country_id',
                'state_id',
                'district_id',
                'address'
            ];

            $allFilled = true;

            foreach ($requiredFields as $field) {
                if (empty($details->$field)) {
                    $allFilled = false;
                    break;
                }
            }

            if ($allFilled) {
                $completed++;
                $userDetailsComplete = true;
            }
        }

        /*
        ==============================
        2️⃣ LINKS CHECK
        ==============================
        */
        $hasLinks = MasterLink::where('user_id', $userId)->exists();
        if ($hasLinks) $completed++;

        /*
        ==============================
        3️⃣ KEYWORD CHECK
        ==============================
        */
        $hasKeywords = MasterKeyword::where('user_id', $userId)->exists();
        if ($hasKeywords) $completed++;

        /*
        ==============================
        4️⃣ BUSINESS INFO CHECK
        ==============================
        */
        $hasBusiness = BusinessInfo::where('user_id', $userId)->exists();
        if ($hasBusiness) $completed++;

        /*
        ==============================
        5️⃣ PRODUCT CHECK
        ==============================
        */
        $hasProduct = Product::where('user_id', $userId)->exists();
        if ($hasProduct) $completed++;

        /*
        ==============================
        FINAL PERCENTAGE
        ==============================
        */
        $percentage = ($completed / $totalMilestones) * 100;

        return response()->json([
            'success' => true,
            'percentage' => (int)$percentage,
            'completed' => $completed,
            'total' => $totalMilestones,
            'milestones' => [
                'user_details' => $userDetailsComplete,
                'links' => $hasLinks,
                'keywords' => $hasKeywords,
                'business_info' => $hasBusiness,
                'product' => $hasProduct,
            ]
        ]);
    }
}