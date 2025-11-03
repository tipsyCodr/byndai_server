<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['user_id', 'amount', 'currency', 'payment_gateway', 'payment_mode', 'order_id', 'status', 'response_data'];
}
