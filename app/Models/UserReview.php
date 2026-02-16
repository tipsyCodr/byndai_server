<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReview extends Model
{
    use HasFactory;

    protected $table = 'user_reviews';

    protected $fillable = [
        'to_user_id',
        'from_user_id',
        'rating',
        'message',
    ];

    // ✅ jisko review mila
    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id', 'id');
    }

    // ✅ jisne review diya
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id', 'id');
    }
}
