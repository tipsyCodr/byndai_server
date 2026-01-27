<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductApiController extends Controller
{
    public function product()
    {
        $users = Auth::user();
        $product = Product::with(['productImage', 'productLink'])->where('user_id', $users->id)->orderBy('id', 'DESC')->get();
        if (!$product) {
            return response()->json(['status' => false, 'message' => 'No product found for this users!'], 404);
        }
        return response()->json(['status' => true, 'data' => $product]);
    }
    public function productLinks($titles, $links, $p_id)
    {
        $createdLinks = [];
        $titles_val = is_array($titles) ? $titles : [$titles];
        $links_val = is_array($links) ? $links : [$links];
        foreach ($titles_val as $key => $title) {
            $linkValue = $links_val[$key] ?? null;
            $createdLinks[] = ProductLink::create(['prod_id' => $p_id, 'title' => $title, 'links' => $linkValue]);
        }
        return $createdLinks;
    }
    public function productImage(Request $r, $title, $files, $p_id)
    {
        foreach ($files as $fileInputName) {
            if ($r->hasFile($fileInputName)) {
                $fileArray = $r->file($fileInputName);
                foreach ($fileArray as $f) {
                    $fileName = time() . '_' . $f->getClientOriginalName();
                    $f->move(public_path('assets/uploads/product/' . $title . '/'), $fileName);
                    ProductImage::create(['prod_id' => $p_id, 'file_name' => $fileName]);
                }
            }
        }
    }
    public function createProduct(Request $r)
    {
        $user = Auth::user();
        $validate = $r->validate([
            'product_name' => 'required|string|max:255',
            'product_price' => 'required|numeric',
            'description' => 'required|string',
            'title_links' => 'nullable|array',
            'links' => 'nullable|array',
            'file_name' => 'nullable|array'
        ]);
        $validate['user_id'] = $user->id;
        $product = Product::create($validate);
        $p_id = $product->id;
        if (!empty($r->title_links) && !empty($r->links)) {
            $this->productLinks($r->title_links, $r->links, $p_id);
        }
        if (!empty($r->file_name)) {
            $this->productImage($r, $r->product_name, $r->file_name, $p_id);
        }
        return response()->json(['status' => true, 'message' => 'Product created successfully', 'product' => $product], 201);
    }
    public function getProductforUpdate($prod_id)
    {
        $user = Auth::id();
        $product = Product::with(['productImage', 'productLink'])->where('user_id', $user)->find($prod_id);
        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found!'], 404);
        }
        return response()->json(['status' => true, 'product' => $product], 200);
    }
    public function updateProduct(Request $r, $prod_id)
    {
        $user = Auth::id();
        $product = Product::where('user_id', $user)->find($prod_id);
        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found!'], 404);
        }
        $validate = $r->validate([
            'product_name' => 'required|string|max:255',
            'product_price' => 'required|numeric',
            'description' => 'required|string',
            'title_links' => 'nullable|array',
            'links' => 'nullable|array',
            'file_name' => 'nullable|array'
        ]);
        $product->update([
            'product_name' => $validate['product_name'],
            'product_price' => $validate['product_price'],
            'description' => $validate['description']
        ]);
        if (!empty($r->title_links) && !empty($r->links)) {
            $this->productLinks($r->title_links, $r->links, $product->id);
        }

        if (!empty($r->file_name)) {
            $this->productImage($r, $product->product_name, $r->file_name, $product->id);
        }
        return response()->json(['status' => true, 'message' => 'Product updated successfully!', 'product' => $product->load(['productImage', 'productLink'])], 200);
    }
    public function deleteProductImage($img_id)
    {
        $image = ProductImage::find($img_id);
        if (!$image) {
            return response()->json(['status' => false, 'message' => 'No product image found!'], 404);
        }
        $image->delete();
        return response()->json(['status' => true, 'message' => 'Product image deleted successfully!'], 200);
    }
    public function deleteProductLinks($link_id)
    {
        $link = ProductLink::find($link_id);
        if (!$link) {
            return response()->json(['status' => false, 'message' => 'No product link found!'], 404);
        }
        $link->delete();
        return response()->json(['status' => true, 'message' => 'Product link deleted successfully!'], 200);
    }
}
