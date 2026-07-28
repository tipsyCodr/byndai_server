<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, $default = null): ?string
    {
        try {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : (string) $default;
        } catch (\Exception $e) {
            return (string) $default;
        }
    }

    public static function set(string $key, $value): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value]
        );
    }
}
