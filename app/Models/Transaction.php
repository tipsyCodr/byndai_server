<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'plan_id',
        'subscription_id',
        'payment_gateway',
        'payment_method',
        'gateway_order_id',
        'gateway_payment_id',
        'gateway_signature',
        'amount',
        'currency',
        'status',
        'raw_response',
        'receipt_no',
        'paid_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'raw_response' => 'json',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'paid_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the plan associated with the transaction.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the subscription associated with the transaction.
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}