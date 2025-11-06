<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['user_id', 'category_id', 'product_name', 'product_price', 'description', 'sku', 'tags', 'stock', 'status'];
    public function productImage()
    {
        return $this->hasMany(ProductImage::class, 'prod_id', 'id');
    }
    public function productLink()
    {
        return $this->hasMany(ProductLink::class, 'prod_id', 'id');
    }
}
