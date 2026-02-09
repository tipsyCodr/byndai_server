<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterEvent extends Model
{
    protected $fillable = ['indus_type', 'title', 'description', 'link', 'start_date', 'end_date', 'timing', 'country_id', 'state_id', 'district_id', 'location', 'event_type', 'max_attendees', 'is_audience', 'is_online', 'status', 'created_by',];
    public function country()
    {
        return $this->belongsTo(MasterCountry::class, 'country_id', 'id');
    }
    public function state()
    {
        return $this->belongsTo(MasterState::class, 'state_id', 'id');
    }
    public function district()
    {
        return $this->belongsTo(MasterDistrict::class, 'district_id', 'id');
    }
    public function Industry()
    {
        return $this->belongsTo(MasterIndustry::class, 'indus_type', 'id');
    }
    public static function getData(Request $r)
    {
        $user_id = Auth::id();
        $query = self::with(['Industry', 'country', 'state', 'district']);
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
                    ->orWhere('description', 'LIKE', "%{$r->title}%");
            });
        }
        if (!empty($r->industry_type)) {
            $query->where('indus_type', $r->industry_type);
        }
        if (!empty($r->country_id)) {
            $query->where('country_id', $r->country_id);
        }
        if (!empty($r->state_id)) {
            $query->where('state_id', $r->state_id);
        }
        if (!empty($r->district_id)) {
            $query->where('district_id', $r->district_id);
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
    public function images()
    {
    return $this->hasMany(MasterEventImage::class, 'event_id', 'id');
    }

    public function createdByUser()
    {
      return $this->belongsTo(User::class, 'created_by', 'id')
        ->with('masterUser');
    }

}
