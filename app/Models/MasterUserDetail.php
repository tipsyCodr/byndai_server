<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterUserDetail extends Model
{
    protected $primaryKey = 'emp_id';
    protected $fillable = ['fname', 'category', 'dept', 'desg', 'mobile_one', 'mobile_two', 'email_one', 'email_two', 'user_id', 'imei_no', 'dob', 'gender', 'country_id', 'state_id', 'district_id', 'address', 'busi_type', 'social_links', 'profile_pic', 'profile_video', 'profile_logo', 'profile_banner'];
}
