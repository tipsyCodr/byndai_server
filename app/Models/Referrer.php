<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Referrer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'full_name',
        'address',
        'aadhaar_photo',
        'status',
        'admin_remarks',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->hasOne(ReferralCoupon::class);
    }

    public function coupons()
    {
        return $this->hasMany(ReferralCoupon::class);
    }

    public function sales()
    {
        return $this->hasMany(ReferralSale::class);
    }

    public function commissions()
    {
        return $this->hasMany(ReferralCommission::class);
    }

    public function payouts()
    {
        return $this->hasMany(CommissionPayout::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved' && $this->is_active;
    }
}
