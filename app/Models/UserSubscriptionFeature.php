<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscriptionFeature extends Model
{
    use HasFactory;

    protected $table = 'user_subscription_features';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'user_subscription_id',
        'feature',        
        'used_value',
        'unit',
        'reset_period',
        'last_reset_date',
    ];

    protected $casts = [
        'used_value'      => 'integer',
        'last_reset_date' => 'datetime',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    protected $attributes = [
        'used_value' => 0,
    ];

    // Relationships
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(UserSubscription::class, 'user_subscription_id', 'id');
    }
    
   

    // Example helper: check if feature usage is exhausted
    public function isExhausted(): bool
    {
        if (!$this->limit_value || $this->limit_value === 'unlimited') {
            return false;
        }
    
        return $this->used_value >= (int) $this->limit_value;
    }

    // Example: remaining value
    public function remaining(): int
    {
        if (!$this->limit_value || $this->limit_value === 'unlimited') {
            return PHP_INT_MAX;
        }
    
        return max(0, (int)$this->limit_value - $this->used_value);
    }
}