<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterLike extends Model
{
    protected $fillable = ['user_id', 'post_id', 'emoji'];
}
