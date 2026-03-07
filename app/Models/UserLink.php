<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLink extends Model
{
    protected $fillable = ['user_id', 'link_id', 'tittle', 'link_data'];

    public function masterLink()
    {
        return $this->belongsTo(MasterLink::class, 'link_id', 'id');
    }
}
