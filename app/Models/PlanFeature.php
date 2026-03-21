<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanFeature extends Model
{
    protected $table = 'plan_features';

    protected $fillable = [
        'plan_id',
        'feature_id',
        'value',
        'unit'
    ];

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }
}