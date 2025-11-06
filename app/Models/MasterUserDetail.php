<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterUserDetail extends Model
{
    protected $primaryKey = 'emp_id';
    protected $fillable = ['fname', 'category', 'dept', 'desg', 'mobile_one', 'mobile_two', 'email_one', 'email_two', 'user_id', 'imei_no', 'dob', 'gender', 'country_id', 'state_id', 'district_id', 'address', 'busi_type', 'social_links', 'profile_pic', 'profile_video', 'profile_logo', 'profile_banner'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
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
}
