<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        "user_mobile",
        "user_name",
        "email",
        "password",
        "otp",
        "created_ip",
        "status",
        "remember_token",
        "google_id",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'pin' => 'hashed',
        ];
    }

    public function masterUser()
    {
        return $this->hasOne(MasterUserDetail::class, 'user_id', 'id');
    }


    public function businessInfo()
    {
        return $this->hasOne(BusinessInfo::class, 'user_id', 'id');
    }

    public function connectionsAsUser()
    {
        return $this->hasMany(Connection::class, 'user_id', 'id');
    }

    public function connectionsAsFriend()
    {
        return $this->hasMany(Connection::class, 'friend_id', 'id');
    }

    public function masterLinks()
    {
        return $this->hasMany(MasterLink::class, 'user_id', 'id');
    }

    public function userLinks()
    {
        return $this->hasMany(UserLink::class, 'user_id', 'id');
    }


}
