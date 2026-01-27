<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPostImage extends Model
{
    protected $fillable = ['post_id', 'post_img', 'type', 'status'];
}
