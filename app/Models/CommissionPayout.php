<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id',
        'amount',
        'payment_method',
        'transaction_reference',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function referrer()
    {
        return $this->belongsTo(Referrer::class);
    }

    public function commissions()
    {
        return $this->hasMany(ReferralCommission::class, 'payout_id');
    }
}
