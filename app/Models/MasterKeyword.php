<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class MasterKeyword extends Model
{
    protected $fillable = ['user_id', 'keywords'];

    public static function getUsersProfile(Request $r)
    {
        // Accept multiple parameter names: 'query', 'search', 'keywords', or 'title'
        $title = $r->input('query') ?? $r->input('search') ?? $r->input('keywords') ?? $r->input('title');
        $query = MasterUserDetail::query();

        if (!empty(trim($title))) {
            $user_ids = MasterKeyword::where('keywords', 'LIKE', "%$title%")->pluck('user_id');
            if ($user_ids->isNotEmpty()) {
                $query->whereIn('user_id', $user_ids);
            } else {
                $query->where(function ($q) use ($title) {
                    $q->where('fname', 'LIKE', "%$title%")->orWhere('dept', 'LIKE', "%$title%")->orWhere('desg', 'LIKE', "%$title%");
                });
            }
        }
        return $query->orderBy('emp_id', 'DESC')->get();
    }
}
