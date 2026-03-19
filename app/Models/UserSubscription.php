<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscription extends Model
{
    use HasFactory;

    protected $table = 'user_subscriptions';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'plan_id',
        'payment_status',
        'payment_id',
        'payment_method',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
        'payment_status'  => 'string',
        'payment_method'  => 'string',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    // Default value for is_active
    protected $attributes = [
        'is_active' => 1,
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function features()
    {
        return $this->hasMany(UserSubscriptionFeature::class, 'user_subscription_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id'); // assuming you have Plan model
    }

    // Helper method example
        public function isActive(): bool
    {
        if (!$this->start_date || !$this->end_date) {
            return false;
        }
    
        return $this->is_active && now()->between($this->start_date, $this->end_date);
    }
}