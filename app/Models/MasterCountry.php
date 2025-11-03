<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterCountry extends Model
{
    protected $table = 'master_countries';
    protected $fillable = ['ctry_name'];
}
