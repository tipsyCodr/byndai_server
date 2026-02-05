<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(MasterUserDetail::class, 'user_id');
    }

    public function media()
    {
        return $this->hasMany(PostMedia::class, 'post_id');
    }

    public function reactions()
    {
        return $this->hasMany(PostReaction::class, 'post_id');
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class, 'post_id');
    }

    public function saves()
    {
        return $this->hasMany(PostSave::class, 'post_id');
    }

    public function shares()
    {
        return $this->hasMany(PostShare::class, 'post_id');
    }

    public function views()
    {
        return $this->hasMany(PostView::class, 'post_id');
    }

    public function reports()
    {
        return $this->hasMany(PostReport::class, 'post_id');
    }
}
