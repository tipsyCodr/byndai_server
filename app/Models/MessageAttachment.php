<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageAttachment extends Model
{
    use HasFactory;
    protected $fillable = ['message_id', 'file_path', 'type'];

    public function message()
    {
        return $this->belongsTo(ChatMessage::class, 'message_id');
    }
}
