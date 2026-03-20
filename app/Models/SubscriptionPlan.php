<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    // Agar table ka naam 'subscription_plans' hai toh ye line optional hai
    protected $table = 'subscription_plans'; 

    protected $fillable = [
        'name', 
        'slug', 
        'description', 
        'type', 
        'is_free', 
        'price', 
        'currency', 
        'status'
    ];

 

    // Data types casting
    protected $casts = [
        'is_free' => 'boolean',
        'price' => 'decimal:2',
        'status' => 'integer',
        'type' => 'string', // kyunki ye enum hai 
    ];

    public function features()
    {
        return $this->hasMany(PlanFeature::class, 'plan_id');
    }
}