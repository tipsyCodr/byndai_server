<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterLink extends Model
{
    protected $fillable = ['name', 'image', 'link', 'status', 'user_id'];
}
