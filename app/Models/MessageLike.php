<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageLike extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'message_id', 'emoji'];

    public function message()
    {
        return $this->belongsTo(ChatMessage::class, 'message_id');
    }
}
