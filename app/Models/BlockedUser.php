<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedUser extends Model
{
    protected $table = 'blocked_users';

    const UPDATED_AT = null;

    protected $fillable = [
        'blocker_id',
        'blocked_id',
        'created_at',
    ];

    /**
     * Check whether either user has blocked the other.
     */
    public static function isBlockedBetween(int $userA, int $userB): bool
    {
        return static::where(function ($q) use ($userA, $userB) {
            $q->where('blocker_id', $userA)->where('blocked_id', $userB);
        })->orWhere(function ($q) use ($userA, $userB) {
            $q->where('blocker_id', $userB)->where('blocked_id', $userA);
        })->exists();
    }

    /**
     * Check whether $blockerId has blocked $blockedId.
     */
    public static function isBlockedBy(int $blockerId, int $blockedId): bool
    {
        return static::where('blocker_id', $blockerId)
            ->where('blocked_id', $blockedId)
            ->exists();
    }
}
