<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterDistrict extends Model
{
    use HasFactory;

    protected $table = 'master_districts';

    protected $fillable = ['id', 'district_name', 's_id'];
}
