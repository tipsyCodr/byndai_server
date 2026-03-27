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
      $plan = SubscriptionPlan::findOrFail($request->plan_id);

      $authId = Auth::id(); // user id

      $amount = $plan->price * 100; // paisa

      $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

      $order = $api->order->create([
          'amount' => $amount,
          'currency' => 'INR',
          'receipt' => 'rcpt_'.$request->user_id.'_'.time()
      ]);

      // DB me save (pending)
      $transaction = Transaction::create([
          'user_id' => $authId,
          'plan_id' => $plan->id,
          'payment_gateway' => 'razorpay',
          'gateway_order_id' => $order['id'],
          'amount' => $plan->price,
          'status' => 'pending'
      ]);

      return response()->json([
          'order_id' => $order['id'],
          'key' => env('RAZORPAY_KEY'),
          'transaction_id' => $transaction->id
      ]);
   }

   public function verifyPayment(Request $request)
   {
       $transaction = Transaction::findOrFail($request->transaction_id);

       // ✅ Already processed check
        if ($transaction->status === 'success') {
            return response()->json([
                'status' => true,
                'message' => 'Already processed'
            ]);
        }
       // Signature verify
       $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        try {
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
            ];

            $api->utility->verifyPaymentSignature($attributes);

        } catch (\Exception $e) {
            $transaction->update(['status' => 'failed']);

            return response()->json([
                'status' => false,
                'message' => 'Payment verification failed'
            ], 400);
        }     
   
        // SUCCESS
        $transaction->update([
           'status' => 'success',
           'gateway_payment_id' => $request->razorpay_payment_id,
           'gateway_signature' => $request->razorpay_signature,
           'paid_at' => now(),
           'raw_response' => json_encode($request->all())
        ]);
   
       // 👉 SUBSCRIPTION UPDATE
       $this->activatePlan($transaction);
   
       return response()->json(['status' => true]);
    }

   // ACTIVATE PLAN
   private function activatePlan($transaction)
   {
     DB::beginTransaction();

      try {
           $userId = $transaction->user_id;

           // Paid plan expired check
           $currentPaid = UserSubscription::where('user_id',      $userId)
               ->where('is_active', 1)
               ->where('plan_id', '!=', $transaction->plan_id)
               ->first();

           if ($currentPaid && $currentPaid->end_date->lt(now()))      {
               $currentPaid->update(['is_active' => 0]);
           }

           // New plan
           $plan = SubscriptionPlan::findOrFail     ($transaction->plan_id);

           // ✅ Duplicate plan prevention
           $existing = UserSubscription::where('user_id', $userId)
               ->where('plan_id', $plan->id)
               ->where('is_active', 1)
               ->first();

           if ($existing) {
               DB::commit();
               return;
           }

           // ✅ Start & End date
           $startDate = now();
           switch ($plan->type) {
               case 'daily': $endDate = now()->addDay(); break;
               case 'weekly': $endDate = now()->addWeek(); break;
               case 'monthly': $endDate = now()->addMonth(); break;
               case 'quarterly': $endDate = now()->addMonths(3); break;
               case 'yearly': $endDate = now()->addYear(); break;
               default: $endDate = now()->addMonth();
           }

           // ✅ Create new subscription
           $subscription = UserSubscription::create([
               'user_id' => $userId,
               'plan_id' => $plan->id,
               'payment_status' => 'paid',
               'payment_id' => $transaction->gateway_payment_id,
               'payment_method' => 'razorpay',
               'start_date' => $startDate,
               'end_date' => $endDate,
               'is_active' => 1,
           ]);

           // ✅ Copy plan features
           $planFeatures = PlanFeature::where('plan_id',      $plan->id)->get();
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

           // ✅ Deactivate old plans only after new plan creation
           UserSubscription::where('user_id', $userId)
               ->where('is_active', 1)
               ->where('id', '!=', $subscription->id)
               ->update(['is_active' => 0]);

           // ✅ Activate free plan if no other active plans
           $activeCount = UserSubscription::where('user_id',      $userId)
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
      } catch (\Exception $e) {
          DB::rollBack();
          Log::error('Subscription activation failed: ' .   $e->getMessage());
      }
   }
   
}