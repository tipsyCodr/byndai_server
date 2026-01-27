<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterCountry extends Model
{
    use HasFactory;
    protected $table = 'master_countries';
    protected $fillable = ['ctry_name'];
}
