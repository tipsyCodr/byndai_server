<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CustomerMaster extends Model
{
    protected $fillable = ['full_name', 'company_name', 'desg', 'mobile_no', 'website', 'email', 'address', 'user_id', 'date', 'front_image_url', 'back_image_url', 'assign_to', 'notes', 'source', 'lead_status', 'cat_id', 'created_by', 'updated_by'];

    public static function getData(Request $r, $user_id)
    {
        $name = $r->name;
        $cat_id = $r->category_id;
        $query = CustomerMaster::query();
        if (!empty($name)) {
            $query->where(function ($q) use ($name) {
                $q->where('full_name', 'like', "%$name%")->orWhere('company_name', 'like', "%$name%")->orWhere('mobile_no', 'like', "%$name%")->orWhere('email', 'like', "%$name%")->orWhere('website', 'like', "%$name%");
            });
        }
        if (!empty($cat_id)) {
            $query->where('cat_id', $cat_id);
        }
        $data = $query->where('user_id', $user_id)->get();
        return $data;
    }
}
