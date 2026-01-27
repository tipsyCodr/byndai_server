<?php

namespace Database\Seeders;

use App\Models\ProductImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['prod_id' => '1', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '1', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '1', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '1', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '1', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '1', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '1', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '1', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '1', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '1', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '2', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '2', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '2', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '2', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '2', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '2', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '2', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '2', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '2', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '2', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '3', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '3', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '3', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '3', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '3', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '3', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '3', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '3', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '3', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '3', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '4', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '4', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '4', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '4', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '4', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '4', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '4', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '4', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '4', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '4', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '5', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '5', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '5', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '5', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '5', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '5', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '5', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '5', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '5', 'file_name' => 'bg3.jpg'],
            ['prod_id' => '5', 'file_name' => 'bg3.jpg'],
        ];
        ProductImage::insert($users);
    }
}
