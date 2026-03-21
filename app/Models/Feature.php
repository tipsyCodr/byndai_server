<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $table = 'features';

    protected $fillable = [
        'feature',
    ];

    /**
     * Ek feature multiple plans me use ho sakta hai
     */
    public function planFeatures()
    {
        return $this->hasMany(PlanFeature::class, 'feature_id');
    }
}