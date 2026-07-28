<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'referral_sale_id',
        'referrer_id',
        'payout_id',
        'amount',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function referrer()
    {
        return $this->belongsTo(Referrer::class);
    }

    public function sale()
    {
        return $this->belongsTo(ReferralSale::class, 'referral_sale_id');
    }

    public function payout()
    {
        return $this->belongsTo(CommissionPayout::class, 'payout_id');
    }
}
