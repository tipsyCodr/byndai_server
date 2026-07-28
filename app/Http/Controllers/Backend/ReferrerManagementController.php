<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Referrer;
use App\Models\ReferralCoupon;
use App\Models\ReferralSale;
use App\Models\ReferralCommission;
use App\Models\CommissionPayout;
use App\Services\ReferralService;
use App\Notifications\ReferrerStatusUpdatedNotification;
use App\Notifications\CommissionPayoutNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReferrerManagementController extends Controller
{
    protected ReferralService $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    public function index(Request $request)
    {
        $query = Referrer::with(['user', 'coupon', 'sales', 'commissions']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('email', 'like', "%{$search}%")
                        ->orWhere('user_mobile', 'like', "%{$search}%");
                  })
                  ->orWhereHas('coupon', function ($cq) use ($search) {
                      $cq->where('code', 'like', "%{$search}%");
                  });
            });
        }

        $referrers = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('backend.referrers.index', compact('referrers'));
    }

    public function show(Referrer $referrer)
    {
        $referrer->load(['user', 'coupon', 'sales.user', 'sales.plan', 'commissions', 'payouts']);
        
        $totalSales = $referrer->sales->count();
        $totalRevenue = $referrer->sales->sum('final_amount');
        $totalEarned = $referrer->sales->sum('commission_amount');
        $pendingCommission = $referrer->commissions->where('status', 'pending')->sum('amount');
        $paidCommission = $referrer->commissions->where('status', 'paid')->sum('amount');

        return view('backend.referrers.show', compact(
            'referrer',
            'totalSales',
            'totalRevenue',
            'totalEarned',
            'pendingCommission',
            'paidCommission'
        ));
    }

    public function updateStatus(Request $request, Referrer $referrer)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,pending',
            'admin_remarks' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($referrer, $validated) {
            $referrer->update([
                'status' => $validated['status'],
                'admin_remarks' => $validated['admin_remarks'] ?? null,
            ]);

            if ($validated['status'] === 'approved') {
                $this->referralService->ensureReferrerCoupon($referrer);
            }

            // Send notification to referrer user
            if ($referrer->user) {
                $referrer->user->notify(new ReferrerStatusUpdatedNotification($referrer));
            }
        });

        return back()->with('success', 'Referrer status updated successfully.');
    }

    public function toggleActive(Referrer $referrer)
    {
        $referrer->update([
            'is_active' => ! $referrer->is_active,
        ]);

        $statusText = $referrer->is_active ? 'enabled' : 'disabled';
        return back()->with('success', "Referrer account has been {$statusText}.");
    }

    public function updateCoupon(Request $request, Referrer $referrer)
    {
        $coupon = $this->referralService->ensureReferrerCoupon($referrer);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('referral_coupons', 'code')->ignore($coupon->id)],
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'commission_type' => 'required|in:percentage,fixed',
            'commission_value' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $coupon->update([
            'code' => strtoupper(trim($validated['code'])),
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'commission_type' => $validated['commission_type'],
            'commission_value' => $validated['commission_value'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Referral coupon and commission rules updated successfully.');
    }

    public function regenerateCode(Referrer $referrer)
    {
        $coupon = $this->referralService->ensureReferrerCoupon($referrer);
        $newCode = $this->referralService->generateUniqueCode($referrer->full_name);

        $coupon->update(['code' => $newCode]);

        return back()->with('success', "New coupon code regenerated: {$newCode}");
    }

    public function sales(Request $request)
    {
        $query = ReferralSale::with(['referrer.user', 'user', 'plan', 'transaction']);

        if ($request->filled('referrer_id')) {
            $query->where('referrer_id', $request->referrer_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(20);
        $referrers = Referrer::where('status', 'approved')->get();

        return view('backend.referrers.sales', compact('sales', 'referrers'));
    }

    public function payouts(Request $request)
    {
        $query = CommissionPayout::with(['referrer']);

        if ($request->filled('referrer_id')) {
            $query->where('referrer_id', $request->referrer_id);
        }

        $payouts = $query->orderBy('paid_at', 'desc')->paginate(20);
        $referrers = Referrer::where('status', 'approved')->get();

        return view('backend.referrers.payouts', compact('payouts', 'referrers'));
    }

    public function storePayout(Request $request)
    {
        $validated = $request->validate([
            'referrer_id' => 'required|exists:referrers,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|max:100',
            'transaction_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $referrer = Referrer::findOrFail($validated['referrer_id']);

        $pendingAmount = ReferralCommission::where('referrer_id', $referrer->id)
            ->where('status', 'pending')
            ->sum('amount');

        if ($validated['amount'] > $pendingAmount) {
            return back()->withErrors(['amount' => "Payout amount cannot exceed pending commission balance of ₹{$pendingAmount}."]);
        }

        $payout = $this->referralService->processPayout(
            $referrer,
            (float) $validated['amount'],
            $validated['payment_method'],
            $validated['transaction_reference'] ?? null,
            $validated['notes'] ?? null
        );

        if ($referrer->user) {
            $referrer->user->notify(new CommissionPayoutNotification($payout));
        }

        return back()->with('success', "Payout of ₹{$payout->amount} recorded successfully.");
    }

    public function settings()
    {
        $settings = [
            'auto_approve_referrers' => \App\Models\ReferralSetting::get('auto_approve_referrers', '0'),
            'default_discount_type' => \App\Models\ReferralSetting::get('default_discount_type', 'percentage'),
            'default_discount_value' => \App\Models\ReferralSetting::get('default_discount_value', '10.00'),
            'default_commission_type' => \App\Models\ReferralSetting::get('default_commission_type', 'percentage'),
            'default_commission_value' => \App\Models\ReferralSetting::get('default_commission_value', '10.00'),
        ];

        return view('backend.referrers.settings', compact('settings'));
    }

    public function updateGlobalSettings(Request $request)
    {
        $validated = $request->validate([
            'auto_approve_referrers' => 'nullable|boolean',
            'default_discount_type' => 'required|in:percentage,fixed',
            'default_discount_value' => 'required|numeric|min:0',
            'default_commission_type' => 'required|in:percentage,fixed',
            'default_commission_value' => 'required|numeric|min:0',
        ]);

        \App\Models\ReferralSetting::set('auto_approve_referrers', $request->boolean('auto_approve_referrers') ? '1' : '0');
        \App\Models\ReferralSetting::set('default_discount_type', $validated['default_discount_type']);
        \App\Models\ReferralSetting::set('default_discount_value', $validated['default_discount_value']);
        \App\Models\ReferralSetting::set('default_commission_type', $validated['default_commission_type']);
        \App\Models\ReferralSetting::set('default_commission_value', $validated['default_commission_value']);

        return back()->with('success', 'Global Referral settings updated successfully.');
    }

    public function resetPassword(Request $request, Referrer $referrer)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = $referrer->user ?? \App\Models\User::find($referrer->user_id);

        if (! $user) {
            return back()->withErrors(['password' => 'User account associated with this referrer was not found.']);
        }

        \Illuminate\Support\Facades\DB::table('users')->where('id', $user->id)->update([
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);

        return back()->with('success', "Password for {$referrer->full_name} updated successfully.");
    }
}
