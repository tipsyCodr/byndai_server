<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterDistrict extends Model
{
    protected $table = 'master_districts';
    protected $fillable = ['district_name', 's_id'];
}
