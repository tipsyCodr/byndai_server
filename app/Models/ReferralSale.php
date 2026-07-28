<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferralSale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'referrer_id',
        'coupon_id',
        'user_id',
        'subscription_plan_id',
        'transaction_id',
        'original_price',
        'discount_amount',
        'final_amount',
        'payment_status',
        'commission_amount',
    ];

    public function referrer()
    {
        return $this->belongsTo(Referrer::class);
    }

    public function coupon()
    {
        return $this->belongsTo(ReferralCoupon::class, 'coupon_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function commission()
    {
        return $this->hasOne(ReferralCommission::class);
    }
}
