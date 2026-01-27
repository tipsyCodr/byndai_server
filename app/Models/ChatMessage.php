<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;
    protected $fillable = ['chat_id', 'sender_id', 'message', 'reply_to_id', 'is_read', 'is_deleted', 'is_delivered'];

    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class, 'message_id');
    }

    public function likes()
    {
        return $this->hasMany(MessageLike::class, 'message_id');
    }

    public function replyTo()
    {
        return $this->belongsTo(ChatMessage::class, 'reply_to_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
