<?php

use Illuminate\Support\Facades\Auth;
use App\Models\MasterUserDetail;

if (!function_exists('current_user')) {
    function current_user()
    {
        return Auth::user();
    }
}

if (!function_exists('current_user_id')) {
    function current_user_id()
    {
        return Auth::id();
    }
}

if (!function_exists('current_user_master')) {
    function current_user_master()
    {
        $user = Auth::user();
        if (!$user) return null;

        return MasterUserDetail::where('user_id', $user->id)->first();
    }
}