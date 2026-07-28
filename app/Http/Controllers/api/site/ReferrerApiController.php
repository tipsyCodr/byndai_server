<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Referrer;
use App\Models\ReferralSale;
use App\Models\ReferralCommission;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class ReferrerApiController extends Controller
{
    protected ReferralService $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Register a new Referrer account (supports existing normal users).
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'user_mobile' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'address' => 'required|string|max:1000',
            'aadhaar_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $user = User::where('email', $validated['email'])
                ->orWhere('user_mobile', $validated['user_mobile'])
                ->first();

            if ($user) {
                if (Referrer::where('user_id', $user->id)->exists()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'A referrer account already exists for this email or mobile number.',
                    ], 422);
                }
            } else {
                $user = User::create([
                    'user_name' => $validated['full_name'],
                    'user_mobile' => $validated['user_mobile'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'status' => '1',
                ]);
            }

            Role::firstOrCreate(['name' => 'Referrer', 'guard_name' => 'web']);
            if (! $user->hasRole('Referrer')) {
                $user->assignRole('Referrer');
            }

            $path = null;
            if ($request->hasFile('aadhaar_photo')) {
                $path = $request->file('aadhaar_photo')->store('aadhaar_cards', 'public');
            }

            $autoApprove = \App\Models\ReferralSetting::get('auto_approve_referrers', '0') === '1';
            $status = $autoApprove ? 'approved' : 'pending';

            $referrer = Referrer::create([
                'user_id' => $user->id,
                'full_name' => $validated['full_name'],
                'address' => $validated['address'],
                'aadhaar_photo' => $path,
                'status' => $status,
                'is_active' => true,
            ]);

            $this->referralService->ensureReferrerCoupon($referrer);

            $message = $autoApprove
                ? 'Referrer account registered and approved successfully!'
                : 'Referrer application submitted successfully! Pending admin approval.';

            return response()->json([
                'status' => true,
                'message' => $message,
                'referrer' => [
                    'id' => $referrer->id,
                    'full_name' => $referrer->full_name,
                    'status' => $referrer->status,
                    'is_active' => $referrer->is_active,
                ],
            ]);
        });
    }

    /**
     * Fetch referrer dashboard statistics.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $referrer = $user->referrer;

        if (! $referrer) {
            return response()->json([
                'status' => false,
                'message' => 'Referrer account not found for this user.',
            ], 404);
        }

        $coupon = $referrer->coupon;
        $salesQuery = ReferralSale::where('referrer_id', $referrer->id);

        $totalSales = (clone $salesQuery)->count();
        $totalRevenue = (float) (clone $salesQuery)->sum('final_amount');
        $totalCommissionEarned = (float) (clone $salesQuery)->sum('commission_amount');

        $pendingCommission = (float) ReferralCommission::where('referrer_id', $referrer->id)
            ->where('status', 'pending')
            ->sum('amount');

        $paidCommission = (float) ReferralCommission::where('referrer_id', $referrer->id)
            ->where('status', 'paid')
            ->sum('amount');

        return response()->json([
            'status' => true,
            'referrer_status' => $referrer->status,
            'is_active' => (bool) $referrer->is_active,
            'coupon_code' => $coupon ? $coupon->code : null,
            'payout_notice' => 'All earned referral commissions are processed and paid out on a weekly basis.',
            'total_plans_sold' => $totalSales,
            'total_revenue' => $totalRevenue,
            'total_commission_earned' => $totalCommissionEarned,
            'pending_commission' => $pendingCommission,
            'paid_commission' => $paidCommission,
        ]);
    }

    /**
     * Fetch referrer sales history (paginated).
     */
    public function sales(Request $request)
    {
        $user = Auth::user();
        $referrer = $user->referrer;

        if (! $referrer) {
            return response()->json([
                'status' => false,
                'message' => 'Referrer account not found for this user.',
            ], 404);
        }

        $sales = ReferralSale::with(['user', 'plan', 'transaction'])
            ->where('referrer_id', $referrer->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'status' => true,
            'data' => $sales,
        ]);
    }

    /**
     * Fetch referrer payouts history (paginated).
     */
    public function payouts(Request $request)
    {
        $user = Auth::user();
        $referrer = $user->referrer;

        if (! $referrer) {
            return response()->json([
                'status' => false,
                'message' => 'Referrer account not found for this user.',
            ], 404);
        }

        $payouts = \App\Models\CommissionPayout::where('referrer_id', $referrer->id)
            ->orderBy('paid_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'status' => true,
            'data' => $payouts,
        ]);
    }
}
