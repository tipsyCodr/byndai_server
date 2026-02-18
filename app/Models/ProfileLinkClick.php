<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileLinkClick extends Model
{
    use HasFactory;

    protected $fillable = [
        'viewer_id',
        'profile_id',
        'platform',
        'click_count',
        'last_clicked_at',
    ];
}
