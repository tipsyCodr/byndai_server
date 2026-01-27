<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLink extends Model
{
    protected $fillable = ['user_id', 'link_id', 'tittle', 'link_data'];

    public function links()
    {
        return $this->hasMany(MasterLink::class, 'id', 'link_id');
    }
}
