<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandKit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'business_category',
        'logo_path',
        'contact_number',
        'whatsapp_number',
        'email',
        'website',
        'address',
        'primary_color',
        'secondary_color',
        'preferred_language',
        'preferred_style',
        'preferred_aspect_ratio',
        'footer_style',
        'show_logo',
        'show_name',
        'show_contact',
        'show_whatsapp',
        'show_address',
        'show_website',
    ];

    protected $casts = [
        'show_logo' => 'boolean',
        'show_name' => 'boolean',
        'show_contact' => 'boolean',
        'show_whatsapp' => 'boolean',
        'show_address' => 'boolean',
        'show_website' => 'boolean',
        'footer_style' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

