<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterIndustry extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'title', 'icons', 'status'];
}
