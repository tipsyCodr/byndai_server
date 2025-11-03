<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessInfo extends Model
{
    protected $fillable = ['legal_name', 'category', 'gst_no', 'address', 'user_id', 'description', 'bank_acc_no', 'ifsc_code', 'branch_name', 'website_url', 'contact_email', 'upi_id', 'payment_qr'];
}
