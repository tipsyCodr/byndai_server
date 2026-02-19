<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileView extends Model
{
    use HasFactory;

    protected $fillable = [
        'viewer_user_id',
        'profile_user_id',
        'view_count',
        'total_time_seconds',
        'last_viewed_at',
    ];

    public function viewer()
    {
        return $this->belongsTo(User::class, 'viewer_user_id', 'id');
    }
}
