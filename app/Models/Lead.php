<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Lead extends Model
{
     protected $fillable = [
        'name',
        'company_name',
        'email',
        'email2',     
        'website', 
        'address',
        'mobile_no_1',
        'mobile_no_2',
        'mobile_no_3',
        'lead_image',      // ✅ Add this
        'image_filename',  // ✅ Add this
    ];
}
