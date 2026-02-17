<?php

namespace App\Http\Controllers\frontend\site;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index($username = null)
    {
        if ($username) {
            $user = \App\Models\User::with(['masterUser', 'businessInfo'])->where('user_name', $username)->first();
            if (!$user && is_numeric($username)) {
                $user = \App\Models\User::with(['masterUser', 'businessInfo'])->find($username);
            }
        } else {
            // Default fallback if needed, or 404
            $user = \App\Models\User::with(['masterUser', 'businessInfo'])->find(1);
        }

        if (!$user) {
            abort(404, 'User not found');
        }

        $products = Product::where('user_id', $user->id)->get();
        $productImages = ProductImage::whereIn('prod_id', $products->pluck('id'))->get();
        return view('components.site.index', compact('user', 'products', 'productImages'));
    }
    public function product($id)
    {
        try {
            $prod_id = decrypt($id);
        } catch (\Exception $e) {
            $prod_id = $id;
        }
        $product = Product::find($prod_id);
        if (!$product) {
            abort(404, 'Product not found');
        }
        $productImages = ProductImage::where('prod_id', $prod_id)->get();
        $similar_products = Product::where('user_id', $product->user_id)->where('id', '!=', $product->id)->orderBY('id', 'DESC')->get();
        $similar_prod_images = ProductImage::whereIn('prod_id', $similar_products->pluck('id'))->get();
        return view('components.site.product', compact('product', 'productImages', 'similar_products', 'similar_prod_images'));
    }
}
