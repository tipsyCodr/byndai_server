<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'amount', 'currency', 'payment_gateway', 'payment_mode', 'order_id', 'status', 'response_data'];
}
