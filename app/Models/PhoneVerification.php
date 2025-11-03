<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneVerification extends Model
{
    protected $table = 'phone_verification';
    protected $fillable = ['user_mobile', 'otp', 'token', 'no_of_attempt', 'verified_at', 'expires_at', 'ip_address'];
}
