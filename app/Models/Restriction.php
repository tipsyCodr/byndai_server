<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restriction extends Model
{
    protected $fillable = ['title', 'slug', 'status'];
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }
}
