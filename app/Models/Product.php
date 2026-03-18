<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_name',
        'product_name',
        'product_price',
        'description',
        'sku',
        'tags',
        'stock',
        'status',
    ];

    public function productImage()
    {
        return $this->hasMany(ProductImage::class, 'prod_id', 'id');
    }

    public function productLink()
    {
        return $this->hasMany(ProductLink::class, 'prod_id', 'id');
    }

    
}
