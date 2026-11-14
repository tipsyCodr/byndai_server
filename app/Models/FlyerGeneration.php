<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FlyerGeneration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'category',
        'occasion_id',
        'prompt',
        'headline',
        'subheadline',
        'language',
        'design_style',
        'aspect_ratio',
        'image_path',
        'raw_ai_image_path',
        'status',
        'error_message',
        'branding_options',
        'generation_metadata',
    ];

    protected $casts = [
        'branding_options' => 'array',
        'generation_metadata' => 'array',
    ];

    protected $appends = [
        'image_url',
        'raw_ai_image_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function occasion()
    {
        return $this->belongsTo(Occasion::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) return null;
        if (str_starts_with($this->image_path, 'http')) return $this->image_path;
        return Storage::disk('public')->url($this->image_path);
    }

    public function getRawAiImageUrlAttribute(): ?string
    {
        if (!$this->raw_ai_image_path) return null;
        if (str_starts_with($this->raw_ai_image_path, 'http')) return $this->raw_ai_image_path;
        return Storage::disk('public')->url($this->raw_ai_image_path);
    }
}

