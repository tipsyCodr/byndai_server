<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flyer extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'image_path', 'thumbnail_path', 'is_active'];

    public function category()
    {
        return $this->belongsTo(FlyerCategory::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
