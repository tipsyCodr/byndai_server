<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessInfo extends Model
{
    protected $fillable = ['legal_name', 'industry_id', 'gst_no', 'address', 'user_id', 'description', 'bank_acc_no', 'ifsc_code', 'branch_name', 'website_url', 'contact_email', 'upi_id', 'payment_qr'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
