<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSave extends Model
{
    protected $fillable = ['user_id', 'post_id'];
    public function post()
    {
        return $this->belongsTo(MasterPost::class, 'post_id', 'id');
    }
}
