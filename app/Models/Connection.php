<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'friend_id', 'connected_at'];
    public function userDetail()
    {
        return $this->hasOne(MasterUserDetail::class, 'user_id', 'user_id');
    }
    public function friendDetail()
    {
        return $this->hasOne(MasterUserDetail::class, 'user_id', 'friend_id');
    }
}
