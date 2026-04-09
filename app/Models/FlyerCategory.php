<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlyerCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'date', 'is_active'];

    public function flyers()
    {
        return $this->hasMany(Flyer::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
