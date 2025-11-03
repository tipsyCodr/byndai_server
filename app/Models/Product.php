<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['user_id', 'category_id', 'product_name', 'product_price', 'description', 'sku', 'tags', 'stock', 'status'];
}
