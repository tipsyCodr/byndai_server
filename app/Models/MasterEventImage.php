<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterEventImage extends Model
{
    protected $fillable = ['event_id', 'event_img', 'type', 'status'];
}
