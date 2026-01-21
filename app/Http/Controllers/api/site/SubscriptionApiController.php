<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Restriction;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubscriptionApiController extends Controller
{
    public function showPlan()
    {
        $plans = Plan::where('status', 1)->latest()->get();
        return response()->json(['status' => true, 'data' => $plans]);
    }
    public function createPlan(Request $r)
    {
        $data = $r->validate([
            'name' => 'required|string|max:50',
            'price' => 'required|numeric',
            'interval' => 'required|string',
            'features' => 'nullable',
            'description' => 'nullable',
            'status' => 'required|in:0,1'
        ]);
        $expload_interval = explode(' ', trim($data['interval']));
        if (count($expload_interval) != 2) {
            return response()->json(['status' => false, 'message' => 'Invalid interval format. Use like "3 months" or "1 year".'], 422);
        }
        $quantity = (int) $expload_interval[0];
        $unit = strtolower($expload_interval[1]);
        if (in_array($unit, ['month', 'months'])) {
            $days = $quantity * 30;
        } elseif (in_array($unit, ['year', 'years'])) {
            $days = $quantity * 365;
        } else {
            return response()->json(['status' => false, 'message' => 'Invalid interval unit. Only "months" or "years" allowed.'], 422);
        }
        $data['duration_days'] = $days;
        $plan = Plan::create($data);
        return response()->json(['status' => true, 'message' => 'Plan created successfully!', 'data' => $plan]);
    }
    public function getPlans($plan_id)
    {
        $plans = Plan::findorFail($plan_id);
        return response()->json(['status' => true, 'data' => $plans]);
    }
    public function getUpdatePlans(Request $r, $plan_id)
    {
        $data = $r->validate([
            'name' => 'required|string|max:50',
            'price' => 'required|numeric',
            'interval' => 'required|string',
            'features' => 'nullable',
            'description' => 'nullable',
            'status' => 'required|in:0,1'
        ]);
        $expload_interval = explode(' ', trim($data['interval']));
        if (count($expload_interval) != 2) {
            return response()->json(['status' => false, 'message' => 'Invalid interval format. Use like "3 months" or "1 year".'], 422);
        }
        $quantity = (int) $expload_interval[0];
        $unit = strtolower($expload_interval[1]);
        if (in_array($unit, ['month', 'months'])) {
            $days = $quantity * 30;
        } elseif (in_array($unit, ['year', 'years'])) {
            $days = $quantity * 365;
        } elseif (in_array($unit, ['day', 'days'])) {
            $days = $quantity;
        } else {
            return response()->json(['status' => false, 'message' => 'Invalid interval unit. Only "months" or "years" allowed.'], 422);
        }
        $data['duration_days'] = $days;
        $plan = Plan::findOrFail($plan_id);
        $plan->update($data);
        return response()->json(['status' => true, 'message' => 'Plan updated successfully!', 'data' => $plan]);
    }
    public function deletePlans($plan_id)
    {
        $plans = Plan::findOrFail($plan_id);
        $plans->delete();
        return response()->json(['status' => true, 'message' => 'Plan deleted successfully!', 'data' => $plans]);
    }
    public function paymentInfo($user_id, $amount)
    {
        try {
            // $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            // $razorAmount = $amount * 100;
            // $order = $api->order->create([
            //     'receipt' => 'order_rcptid_' . uniqid(),
            //     'amount' => $razorAmount,
            //     'currency' => 'INR',
            //     'payment_capture' => 1
            // ]);

            $payment = Payment::create(['user_id' => $user_id, 'order_id' => '12345', 'amount' => $amount, 'currency' => 'INR', 'payment_gateway' => 'razorpay', 'payment_mode' => 'online', 'status' => 1, 'response_data' => json_encode([])]);
            return ['status' => true, 'message' => 'Payment initiated successfully', 'payment' => $payment];
        } catch (\Exception $e) {
            Log::error('Payment Error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Payment initiation failed: ' . $e->getMessage()];
        }
    }

    public function createSubscription(Request $r)
    {
        try {
            $data = $r->validate(['plan_id' => 'required|exists:plans,id', 'start_date' => 'required|date', 'status' => 'required|in:0,1', 'renewal_status' => 'nullable', 'cancelled_at' => 'nullable|date', 'cancel_reason' => 'nullable|string']);

            $user = Auth::id();
            $plan = Plan::findOrFail($data['plan_id']);
            $interval = trim($plan->interval);
            $exploded_interval = explode(' ', $interval);
            if (count($exploded_interval) != 2) {
                return response()->json(['status' => false, 'message' => 'Invalid interval format. Use like "3 months" or "1 year".'], 422);
            }
            $quantity = (int) $exploded_interval[0];
            $unit = strtolower($exploded_interval[1]);
            $startDate = Carbon::parse($data['start_date']);
            if (in_array($unit, ['month', 'months'])) {
                $endDate = $startDate->copy()->addMonths($quantity);
            } elseif (in_array($unit, ['year', 'years'])) {
                $endDate = $startDate->copy()->addYears($quantity);
            } else {
                return response()->json(['status' => false, 'message' => 'Invalid interval unit. Only "months" or "years" allowed.'], 422);
            }
            Subscription::where('user_id', $user)->where('end_date', '<', now())->update(['status' => 0]);
            $activeSubscriptionExists = Subscription::where('user_id', $user)->exists();
            $data['renewal_status'] = $activeSubscriptionExists ? 9 : 10;
            $data['user_id'] = $user;
            $data['end_date'] = $endDate->format('Y-m-d H:i:s');
            $data['amount'] = $plan->price;
            $payment = $this->paymentInfo($data['user_id'], $data['amount']);
            if (!$payment['status']) {
                return response()->json(['status' => false, 'message' => 'Payment initiation failed.']);
            }
            $data['payment_id'] = $payment['payment']->id;
            $subscription = Subscription::create($data);
            return response()->json(['status' => true, 'message' => 'Subscription created successfully!', 'data' => $subscription]);
        } catch (\Exception $e) {
            Log::error('Subscription Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Subscription creation failed: ' . $e->getMessage()], 500);
        }
    }

    public function checkPaidPlans(Request $r)
    {
        $user = Auth::id();
        // $slugs = $r->slugs;
        $subscription = Subscription::where('user_id', $user)->where('status', 1)->orderBy('id', 'DESC')->first();
        if (!$subscription) {
            return response()->json(['status' => false, 'message' => 'User does not have any active subscription.'], 401);
        }
        if ($subscription->start_date <= now() && $subscription->end_date >= now()) {
            $plan_id = $subscription->plan_id;
            // $restriction = Restriction::whereIn('slug', $slugs)->where('plan_id', $plan_id)->get();
            $restriction = Restriction::with('plan')->where('plan_id', $plan_id)->get();
            return response()->json(['status' => true, 'message' => 'Active subscription. Access granted.', 'features' => $restriction]);
        } else {
            return response()->json(['status' => false, 'message' => 'Subscription is expired or not yet started.'], 403);
        }
    }
}
