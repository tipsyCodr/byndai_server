<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterState extends Model
{
    use HasFactory;

    protected $table = 'master_states';

    protected $fillable = ['id', 'state_name', 'c_id'];

    public static function getAllStates()
    {
        return self::orderBy('state_name', 'ASC')->get();
    }
}
