<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\PlanFeature;
use App\Models\User;
use App\Models\Transaction;
use App\Models\UserSubscription;
use App\Models\UserSubscriptionFeature;
use App\Models\MasterUserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Razorpay\Api\Api;

class TransactionApiController extends Controller
{
   public function createOrder(Request $request)
   {
      $validated = $request->validate([
          'plan_id' => 'required|integer|exists:subscription_plans,id',
      ]);

      $authId = Auth::id(); // user id

      $plan = SubscriptionPlan::where('id', $validated['plan_id'])
          ->where('status', 1)
          ->where('is_free', false)
          ->first();

      if (! $plan || $plan->price <= 0) {
          return response()->json([
              'status' => false,
              'message' => 'Selected plan is not available for payment'
          ], 422);
      }

      $activeSamePlan = UserSubscription::where('user_id', $authId)
          ->where('plan_id', $plan->id)
          ->where('is_active', 1)
          ->where('end_date', '>=', now())
          ->exists();

      if ($activeSamePlan) {
          return response()->json([
              'status' => false,
              'message' => 'This plan is already active'
          ], 409);
      }

      $amount = (int) round((float) $plan->price * 100); // paisa

      $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

      $order = $api->order->create([
          'amount' => $amount,
          'currency' => 'INR',
          'receipt' => 'rcpt_'.$authId.'_'.time(),
          'payment_capture' => true,
      ]);

      // DB me save (pending)
      $transaction = Transaction::create([
          'user_id' => $authId,
          'plan_id' => $plan->id,
          'payment_gateway' => 'razorpay',
          'payment_method' => 'online',
          'gateway_order_id' => $order['id'],
          'amount' => $plan->price,
          'currency' => 'INR',
          'status' => 'pending'
      ]);

      return response()->json([
          'status' => true,
          'order_id' => $order['id'],
          'key' => config('services.razorpay.key'),
          'transaction_id' => $transaction->id,
          'amount' => $amount,
          'currency' => 'INR',
      ]);
   }

   public function verifyPayment(Request $request)
   {
       $validated = $request->validate([
           'transaction_id' => 'required|integer|exists:transactions,id',
           'razorpay_order_id' => 'required|string',
           'razorpay_payment_id' => 'required|string',
           'razorpay_signature' => 'required|string',
       ]);

       $transaction = Transaction::where('id', $validated['transaction_id'])
           ->where('user_id', Auth::id())
           ->firstOrFail();

       // ✅ Already processed check
        if ($transaction->status === 'success') {
            $hasSubscription = UserSubscription::where('user_id', $transaction->user_id)
                ->where('plan_id', $transaction->plan_id)
                ->where('payment_id', $transaction->gateway_payment_id)
                ->exists();

            if (! $hasSubscription && ! $this->activatePlan($transaction)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Payment verified, but subscription activation failed'
                ], 500);
            }

            return response()->json([
                'status' => true,
                'message' => 'Already processed'
            ]);
        }
       if ($validated['razorpay_order_id'] !== $transaction->gateway_order_id) {
           return response()->json([
               'status' => false,
               'message' => 'Payment order does not match this transaction'
           ], 400);
       }

       // Signature verify
       $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        try {
            $attributes = [
                'razorpay_order_id' => $validated['razorpay_order_id'],
                'razorpay_payment_id' => $validated['razorpay_payment_id'],
                'razorpay_signature' => $validated['razorpay_signature'],
            ];

            $api->utility->verifyPaymentSignature($attributes);
            $payment = $api->payment->fetch($validated['razorpay_payment_id']);

        } catch (\Exception $e) {
            $transaction->update(['status' => 'failed']);

            return response()->json([
                'status' => false,
                'message' => 'Payment verification failed'
            ], 400);
        }

        $expectedAmount = (int) round((float) $transaction->amount * 100);
        $expectedCurrency = $transaction->currency ?: 'INR';
        $paymentStatus = $payment['status'] ?? null;
        $paymentCaptured = (bool) ($payment['captured'] ?? false);

        if (($payment['order_id'] ?? null) !== $transaction->gateway_order_id) {
            $transaction->update(['status' => 'failed']);

            return response()->json([
                'status' => false,
                'message' => 'Razorpay payment is not linked to this order'
            ], 400);
        }

        if ((int) ($payment['amount'] ?? 0) !== $expectedAmount || ($payment['currency'] ?? null) !== $expectedCurrency) {
            $transaction->update(['status' => 'failed']);

            return response()->json([
                'status' => false,
                'message' => 'Paid amount does not match the selected plan'
            ], 400);
        }

        if ($paymentStatus !== 'captured' && ! $paymentCaptured) {
            return response()->json([
                'status' => false,
                'message' => 'Payment is not captured yet'
            ], 400);
        }
   
        // SUCCESS
        $transaction->update([
           'status' => 'success',
           'gateway_payment_id' => $validated['razorpay_payment_id'],
           'gateway_signature' => $validated['razorpay_signature'],
           'paid_at' => now(),
           'raw_response' => $payment->toArray()
        ]);
   
       // 👉 SUBSCRIPTION UPDATE
       if (! $this->activatePlan($transaction)) {
           return response()->json([
               'status' => false,
               'message' => 'Payment verified, but subscription activation failed'
           ], 500);
       }
   
       return response()->json(['status' => true]);
    }

   // ACTIVATE PLAN
   private function activatePlan($transaction): bool
   {
      DB::beginTransaction();

      try {
           $userId = $transaction->user_id;

           // Paid plan expired check
           $currentPaid = UserSubscription::where('user_id', $userId)
               ->where('is_active', 1)
               ->where('plan_id', '!=', $transaction->plan_id)
               ->first();

           if ($currentPaid && $currentPaid->end_date && $currentPaid->end_date->lt(now())) {
               $currentPaid->update(['is_active' => 0]);
           }

           // New plan
           $plan = SubscriptionPlan::findOrFail($transaction->plan_id);

           // ✅ Find the latest subscription to queue after
           $latestSub = UserSubscription::where('user_id', $userId)
               ->where('is_active', 1)
               ->orderBy('end_date', 'desc')
               ->first();

           // ✅ Start & End date (Queue after latest expires, or NOW if none)
           $startDate = ($latestSub && $latestSub->end_date && $latestSub->end_date > now()) ? $latestSub->end_date : now();

           switch ($plan->type) {
               case 'daily': $endDate = $startDate->copy()->addDay(); break;
               case 'weekly': $endDate = $startDate->copy()->addWeek(); break;
               case 'monthly': $endDate = $startDate->copy()->addMonth(); break;
               case 'quarterly': $endDate = $startDate->copy()->addMonths(3); break;
               case 'yearly': $endDate = $startDate->copy()->addYear(); break;
               default: $endDate = $startDate->copy()->addMonth();
           }

           // ✅ Create new subscription
           $subscription = UserSubscription::create([
               'user_id' => $userId,
               'plan_id' => $plan->id,
               'payment_status' => 'paid',
               'payment_id' => $transaction->gateway_payment_id,
               'payment_method' => 'online',
               'start_date' => $startDate,
               'end_date' => $endDate,
               'is_active' => 1,
           ]);

           $transaction->update(['subscription_id' => $subscription->id]);

           // ✅ Copy plan features
           $planFeatures = PlanFeature::where('plan_id', $plan->id)->get();
           foreach ($planFeatures as $feature) {
               UserSubscriptionFeature::create([
                   'user_subscription_id' => $subscription->id,
                   'feature_id' => $feature->feature_id,
                   'used_value' => 0,
                   'unit' => $feature->unit,
                   'reset_period' => $plan->type,
                   'last_reset_date' => now(),
               ]);
           }

           // ✅ Activate free plan if no other active plans
           $activeCount = UserSubscription::where('user_id', $userId)
               ->where('is_active', 1)
               ->count();

           if ($activeCount == 0) {
               $freePlan = SubscriptionPlan::where('is_free', 1)->first();
               if ($freePlan) {
                   $start = now();
                   switch ($freePlan->type) {
                       case 'daily': $end = now()->addDay(); break;
                       case 'weekly': $end = now()->addWeek(); break;
                       case 'monthly': $end = now()->addMonth(); break;
                       case 'quarterly': $end = now()->addMonths(3); break;
                       case 'yearly': $end = now()->addYear(); break;
                       default: $end = now()->addMonth();
                   }

                   $freeSub = UserSubscription::create([
                       'user_id' => $userId,
                       'plan_id' => $freePlan->id,
                       'payment_status' => null,
                       'payment_id' => null,
                       'payment_method' => null,
                       'start_date' => $start,
                       'end_date' => $end,
                       'is_active' => 1,
                   ]);

                   $freeFeatures = PlanFeature::where('plan_id', $freePlan->id)->get();
                   foreach ($freeFeatures as $feature) {
                       UserSubscriptionFeature::create([
                           'user_subscription_id' => $freeSub->id,
                           'feature_id' => $feature->feature_id,
                           'used_value' => 0,
                           'unit' => $feature->unit,
                           'reset_period' => $freePlan->type,
                           'last_reset_date' => now(),
                       ]);
                   }
               }
           }

           DB::commit();
           return true;
      } catch (\Exception $e) {
          DB::rollBack();
          Log::error('Subscription activation failed: ' . $e->getMessage(), [
              'transaction_id' => $transaction->id,
              'user_id' => $transaction->user_id,
              'plan_id' => $transaction->plan_id,
          ]);
          return false;
      }
   }
   
}
