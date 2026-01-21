<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConnectionRequest extends Model
{
    use HasFactory;
    protected $fillable = ['sender_id', 'receiver_id', 'status', 'request_type', 'requested_at', 'responded_at'];
    public function sender()
    {
        return $this->belongsTo(MasterUserDetail::class, 'user_id', 'sender_id');
    }
    public function senderDetail()
    {
        return $this->belongsTo(MasterUserDetail::class, 'sender_id', 'user_id');
    }
    public function receiverDetail()
    {
        return $this->belongsTo(MasterUserDetail::class, 'receiver_id', 'user_id');
    }
}
