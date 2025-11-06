<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterPost extends Model
{
    protected $fillable = ['indus_type', 'title', 'content', 'slug', 'is_audience', 'like', 'comments', 'status', 'created_by'];
    public function industry()
    {
        return $this->belongsTo(MasterIndustry::class, 'indus_type', 'id');
    }
    public function createdBy()
    {
        return $this->belongsTo(MasterUserDetail::class, 'created_by', 'user_id');
    }
    public static function getData(Request $r)
    {
        $user_id = Auth::id();
        $query = self::with(['industry', 'createdBy']);
        if ($r->is_audience == 1) {
            $connections = Connection::where('user_id', $user_id)->pluck('friend_id')->toArray();
            $query->where(function ($q) use ($connections, $user_id) {
                $q->whereIn('created_by', $connections)
                    ->orWhere('created_by', $user_id);
            });
        } else {
            $query->where('is_audience', 0);
        }
        if (!empty($r->title)) {
            $query->where(function ($q) use ($r) {
                $q->where('title', 'LIKE', "%{$r->title}%")
                    ->orWhere('content', 'LIKE', "%{$r->title}%");
            });
        }
        if (!empty($r->industry_type)) {
            $query->where('indus_type', $r->industry_type);
        }
        if (!empty($r->from_date) && !empty($r->to_date)) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$r->from_date, $r->to_date]);
        } elseif (!empty($r->from_date)) {
            $query->whereDate('created_at', '>=', $r->from_date);
        } elseif (!empty($r->to_date)) {
            $query->whereDate('created_at', '<=', $r->to_date);
        }
        return $query->latest()->get();
    }
}
