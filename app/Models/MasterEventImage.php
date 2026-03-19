<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterEventImage extends Model
{
    protected $fillable = ['event_id', 'event_img', 'event_img_path',  'type', 'status'];

     public function event()
    {
        return $this->belongsTo(MasterEvent::class, 'event_id', 'id');
    }
}
