<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterState extends Model
{
    protected $table = 'master_states';
    protected $fillable = ['state_name', 'c_id'];
}
