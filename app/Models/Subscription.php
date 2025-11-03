<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['user_id', 'plan_id', 'start_date', 'end_date', 'trial_end_date', 'status', 'payment_id', 'renewal_status', 'cancelled_at', 'cancel_reason'];
}
