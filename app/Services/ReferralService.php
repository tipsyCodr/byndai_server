<?php

namespace App\Services;

use App\Models\Referrer;
use App\Models\ReferralCoupon;
use App\Models\ReferralSale;
use App\Models\ReferralCommission;
use App\Models\CommissionPayout;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReferralService
{
    public function generateUniqueCode(?string $name = null): string
    {
        do {
            if (! empty($name)) {
                $cleanName = strtoupper(preg_replace('/[^A-Z]/i', '', $name));
                $prefix = substr($cleanName, 0, 3);
                if (strlen($prefix) < 3) {
                    $prefix = str_pad($prefix, 3, 'X');
                }
            } else {
                $prefix = Str::upper(Str::random(3));
            }

            $numbers = str_pad((string) random_int(10005, 99999), 5, '0', STR_PAD_LEFT);
            $code = $prefix . $numbers;
        } while (ReferralCoupon::where('code', $code)->exists());

        return $code;
    }

    /**
     * Create or assign default coupon for approved referrer.
     */
    public function ensureReferrerCoupon(Referrer $referrer): ReferralCoupon
    {
        if ($referrer->coupon) {
            return $referrer->coupon;
        }

        return ReferralCoupon::create([
            'referrer_id' => $referrer->id,
            'code' => $this->generateUniqueCode($referrer->full_name),
            'discount_type' => 'percentage',
            'discount_value' => 10.00,
            'commission_type' => 'percentage',
            'commission_value' => 10.00,
            'is_active' => true,
        ]);
    }

    /**
     * Validate coupon code for a given customer & plan.
     */
    public function validateCoupon(string $code, float $originalPrice): array
    {
        $code = strtoupper(trim($code));
        $coupon = ReferralCoupon::with('referrer')
            ->where('code', $code)
            ->where('is_active', true)
            ->first();

        if (! $coupon) {
            return [
                'valid' => false,
                'message' => 'Invalid or inactive coupon code.',
            ];
        }

        if (! $coupon->referrer || ! $coupon->referrer->isApproved()) {
            return [
                'valid' => false,
                'message' => 'This coupon code belongs to an unverified referrer.',
            ];
        }

        $discountAmount = $coupon->calculateDiscount($originalPrice);
        $finalAmount = max(0, $originalPrice - $discountAmount);

        return [
            'valid' => true,
            'coupon' => $coupon,
            'referrer' => $coupon->referrer,
            'original_price' => $originalPrice,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
        ];
    }

    /**
     * Process referral sale and record commission upon successful transaction.
     */
    public function recordReferralSale(Transaction $transaction): ?ReferralSale
    {
        if (! $transaction->coupon_id || ! $transaction->referrer_id) {
            return null;
        }

        return DB::transaction(function () use ($transaction) {
            $coupon = ReferralCoupon::find($transaction->coupon_id);
            $referrer = Referrer::find($transaction->referrer_id);

            if (! $coupon || ! $referrer) {
                return null;
            }

            // Check duplicate sale record for transaction
            $existingSale = ReferralSale::where('transaction_id', $transaction->id)->first();
            if ($existingSale) {
                return $existingSale;
            }

            $originalPrice = (float) ($transaction->amount + $transaction->discount_amount);
            $finalPaidAmount = (float) $transaction->amount;
            $commissionAmount = $coupon->calculateCommission($finalPaidAmount, $originalPrice);

            $sale = ReferralSale::create([
                'referrer_id' => $referrer->id,
                'coupon_id' => $coupon->id,
                'user_id' => $transaction->user_id,
                'subscription_plan_id' => $transaction->plan_id,
                'transaction_id' => $transaction->id,
                'original_price' => $originalPrice,
                'discount_amount' => $transaction->discount_amount,
                'final_amount' => $finalPaidAmount,
                'payment_status' => 'paid',
                'commission_amount' => $commissionAmount,
            ]);

            ReferralCommission::create([
                'referral_sale_id' => $sale->id,
                'referrer_id' => $referrer->id,
                'amount' => $commissionAmount,
                'status' => 'pending',
            ]);

            return $sale;
        });
    }

    /**
     * Process commission payout for referrer.
     */
    public function processPayout(Referrer $referrer, float $amount, ?string $paymentMethod = null, ?string $transactionRef = null, ?string $notes = null): CommissionPayout
    {
        return DB::transaction(function () use ($referrer, $amount, $paymentMethod, $transactionRef, $notes) {
            $payout = CommissionPayout::create([
                'referrer_id' => $referrer->id,
                'amount' => $amount,
                'payment_method' => $paymentMethod ?? 'bank_transfer',
                'transaction_reference' => $transactionRef,
                'notes' => $notes,
                'paid_at' => now(),
            ]);

            // Mark pending commissions as paid up to amount
            $pendingCommissions = ReferralCommission::where('referrer_id', $referrer->id)
                ->where('status', 'pending')
                ->orderBy('created_at', 'asc')
                ->get();

            $remainingToPay = $amount;
            foreach ($pendingCommissions as $commission) {
                if ($remainingToPay <= 0) {
                    break;
                }

                $commission->update([
                    'status' => 'paid',
                    'payout_id' => $payout->id,
                    'paid_at' => now(),
                ]);

                $remainingToPay -= $commission->amount;
            }

            return $payout;
        });
    }
}
