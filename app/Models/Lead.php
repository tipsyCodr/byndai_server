<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Lead extends Model
{
     protected $fillable = [
        'user_id',
        'category',
        'name',
        'company_name',
        'email',
        'email2',     
        'website', 
        'address',
        'mobile_no_1',
        'mobile_no_2',
        'mobile_no_3',
        'lead_image',      
        'image_filename',  
        'selfy_image_1',
        'selfy_image_2',
        'selfy_video',
    ];
}

