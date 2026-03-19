<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductClick extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'viewer_id',
        'profile_id',
        'click_count',
        'last_clicked_at',
    ];

    protected $casts = [
        'last_clicked_at' => 'datetime',
    ];

    // 👇 Relationships
    public function viewer()
    {
        return $this->belongsTo(User::class, 'viewer_id');
    }

    public function profile()
    {
        return $this->belongsTo(User::class, 'profile_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function viewerDetail()
    {
        return $this->hasOne(MasterUserDetail::class, 'user_id', 'viewer_id');
    }
}
