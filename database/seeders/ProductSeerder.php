<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeerder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['user_id' => 1, 'category_id' => 1, 'product_name' => 'Apple iPhone 15 Pro', 'product_price' => 124999.00, 'description' => 'Apple iPhone 15 Pro with A17 Pro chip, 48MP camera, and Titanium body.', 'sku' => 'IP15PRO-BLACK-128', 'tags' => 'iphone,apple,smartphone,mobile', 'stock' => 50, 'status' => 1,],
            ['user_id' => 1, 'category_id' => 2, 'product_name' => 'Samsung Galaxy S24 Ultra', 'product_price' => 119999.00, 'description' => 'Samsung flagship smartphone with 200MP camera and Snapdragon 8 Gen 3.', 'sku' => 'SGS24U-BLACK-256', 'tags' => 'samsung,android,smartphone', 'stock' => 30, 'status' => 1,],
            ['user_id' => 2, 'category_id' => 3, 'product_name' => 'Sony WH-1000XM5 Headphones', 'product_price' => 29990.00, 'description' => 'Industry-leading noise-cancelling wireless headphones with superior sound.', 'sku' => 'SONYXM5-BLACK', 'tags' => 'sony,headphones,audio,wireless', 'stock' => 100, 'status' => 1,],
            ['user_id' => 3, 'category_id' => 4, 'product_name' => 'Dell XPS 13 Laptop', 'product_price' => 129990.00, 'description' => 'Premium ultrabook with Intel i7 processor, 16GB RAM, and 512GB SSD.', 'sku' => 'DELLXPS13-SILVER', 'tags' => 'laptop,dell,ultrabook,notebook', 'stock' => 20, 'status' => 1,],
            ['user_id' => 2, 'category_id' => 5, 'product_name' => 'Nike Air Max 270', 'product_price' => 12999.00, 'description' => 'Comfortable and stylish running shoes with large Air Max cushioning.', 'sku' => 'NIKEAIR270-RED', 'tags' => 'nike,shoes,sports,running', 'stock' => 200, 'status' => 1,],
        ];
        Product::insert($products);
    }
}
