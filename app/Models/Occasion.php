<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Occasion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'date',
        'year',
        'region',
        'language',
        'is_active',
        'sample_prompts',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'year' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDate($query, string $date, ?int $year = null)
    {
        return $query->where('date', $date)
            ->where(function ($q) use ($year) {
                $q->whereNull('year');
                if ($year) {
                    $q->orWhere('year', $year);
                }
            });
    }

    public function generations()
    {
        return $this->hasMany(FlyerGeneration::class);
    }
}

