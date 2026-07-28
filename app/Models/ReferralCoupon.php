<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferralCoupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'referrer_id',
        'code',
        'discount_type',
        'discount_value',
        'commission_type',
        'commission_value',
        'is_active',
    ];

    public function referrer()
    {
        return $this->belongsTo(Referrer::class);
    }

    public function sales()
    {
        return $this->hasMany(ReferralSale::class, 'coupon_id');
    }

    public function calculateDiscount(float $originalPrice): float
    {
        if ($this->discount_type === 'percentage') {
            return round(($originalPrice * $this->discount_value) / 100, 2);
        }

        return min($originalPrice, (float) $this->discount_value);
    }

    public function calculateCommission(float $finalPaidAmount, float $originalPrice): float
    {
        if ($this->commission_type === 'percentage') {
            return round(($finalPaidAmount * $this->commission_value) / 100, 2);
        }

        return (float) $this->commission_value;
    }
}
