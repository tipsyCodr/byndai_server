<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterComment extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'post_id', 'comment', 'parent_id', 'status'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function replies()
    {
        return $this->hasMany(MasterComment::class, 'parent_id')->with('replies', 'user', 'likes');
    }
    public function likes()
    {
        return $this->hasMany(MasterCommentLike::class, 'comment_id');
    }
}
