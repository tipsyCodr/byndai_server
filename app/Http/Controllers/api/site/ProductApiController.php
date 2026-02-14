<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductApiController extends Controller
{
    // ==========================================================
    // 1) GET ALL PRODUCTS (search + sort + pagination)    
    // ==========================================================
    public function index(Request $request)
    {
        try {
            $perPage = 10;

            $search = $request->query('search');
            $sortBy = $request->query('sort_by', 'id');
            $sortOrder = $request->query('sort_order', 'desc');

            // allowed sort fields
            $allowedSort = ['id', 'product_name', 'product_price', 'created_at', 'updated_at'];
            if (! in_array($sortBy, $allowedSort)) {
                $sortBy = 'id';
            }

            if (! in_array(strtolower($sortOrder), ['asc', 'desc'])) {
                $sortOrder = 'desc';
            }

            $query = Product::with(['productImage', 'productLink']);

            // search
            if (! empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('product_name', 'like', "%$search%")
                        ->orWhere('category_name', 'like', "%$search%")
                        ->orWhere('sku', 'like', "%$search%")
                        ->orWhere('tags', 'like', "%$search%");
                });
            }

            $products = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

            return response()->json([
                'status' => true,
                'message' => 'Products fetched successfully',
                'data' => $products,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ==========================================================
    // 2) GET ALL PRODUCTS BY USER ID (search + sort + pagination)    
    // ==========================================================
    public function productsByUser(Request $request, $user_id)
    {
        try {
            $perPage = 10;

            $search = $request->query('search');
            $sortBy = $request->query('sort_by', 'id');
            $sortOrder = $request->query('sort_order', 'desc');

            $allowedSort = ['id', 'product_name', 'product_price', 'created_at', 'updated_at'];
            if (! in_array($sortBy, $allowedSort)) {
                $sortBy = 'id';
            }

            if (! in_array(strtolower($sortOrder), ['asc', 'desc'])) {
                $sortOrder = 'desc';
            }

            $query = Product::with(['productImage', 'productLink'])
                ->where('user_id', $user_id);

            if (! empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('product_name', 'like', "%$search%")
                        ->orWhere('category_name', 'like', "%$search%")
                        ->orWhere('sku', 'like', "%$search%")
                        ->orWhere('tags', 'like', "%$search%");
                });
            }

            $products = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

            return response()->json([
                'status' => true,
                'message' => 'User products fetched successfully',
                'data' => $products,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ==========================================================
    // 3) GET PRODUCT BY ID  
    // ==========================================================
    public function show($id)
    {
        try {
            $product = Product::with(['productImage', 'productLink'])->find($id);

            if (! $product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Product fetched successfully',
                'data' => $product,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ==========================================================
    // 5) ADD PRODUCT    
    // ==========================================================
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            $validator = Validator::make($request->all(), [
                'category_name' => 'nullable|string|max:255',
                'product_name' => 'required|string|max:255',
                'product_price' => 'required|numeric',
                'description' => 'nullable|string',
                'sku' => 'nullable|string|max:255',
                'tags' => 'nullable|string|max:255',
                'stock' => 'nullable|numeric',
                'status' => 'nullable|in:0,1',

                // image
                'product_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            $product = Product::create([
                'user_id' => $user->id,
                'category_name' => $request->category_name,
                'product_name' => $request->product_name,
                'product_price' => $request->product_price,
                'description' => $request->description,
                'sku' => $request->sku,
                'tags' => $request->tags,
                'stock' => $request->stock,
                'status' => $request->status ?? 1,
            ]);

            // ✅ Image upload (save into ProductImage table)
            if ($request->hasFile('product_image')) {

                $file = $request->file('product_image');

                $folder = public_path('uploads/products');
                if (! File::exists($folder)) {
                    File::makeDirectory($folder, 0777, true);
                }

                $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                $file->move($folder, $fileName);

                ProductImage::create([
                    'prod_id' => $product->id,
                    'file_name' => 'uploads/products/'.$fileName,
                    'status' => 1,
                ]);
            }

            // ✅ Links (optional)
            // frontend can send: links = [{"title":"Amazon","links":"https://..."}, ...]
            if ($request->links) {
                $links = $request->links;

                // if string -> decode
                if (is_string($links)) {
                    $links = json_decode($links, true);
                }

                if (is_array($links)) {
                    foreach ($links as $l) {
                        if (! empty($l['links'])) {
                            ProductLink::create([
                                'prod_id' => $product->id,
                                'title' => $l['title'] ?? 'Link',
                                'links' => $l['links'],
                                'status' => 1,
                            ]);
                        }
                    }
                }
            }

            $final = Product::with(['productImage', 'productLink'])->find($product->id);

            return response()->json([
                'status' => true,
                'message' => 'Product created successfully',
                'data' => $final,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ==========================================================
    // 4) UPDATE PRODUCT  
    // ==========================================================
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();

            $product = Product::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (! $product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'category_name' => 'nullable|string|max:255',
                'product_name' => 'required|string|max:255',
                'product_price' => 'required|numeric',
                'description' => 'nullable|string',
                'sku' => 'nullable|string|max:255',
                'tags' => 'nullable|string|max:255',
                'stock' => 'nullable|numeric',
                'status' => 'nullable|in:0,1',

                'product_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            $product->update([
                'category_name' => $request->category_name,
                'product_name' => $request->product_name,
                'product_price' => $request->product_price,
                'description' => $request->description,
                'sku' => $request->sku,
                'tags' => $request->tags,
                'stock' => $request->stock,
                'status' => $request->status ?? $product->status,
            ]);

            // ✅ If new image uploaded => delete old + insert new
            if ($request->hasFile('product_image')) {

                // delete old images from storage + table
                $oldImages = ProductImage::where('prod_id', $product->id)->get();
                foreach ($oldImages as $img) {
                    $path = public_path($img->file_name);
                    if (File::exists($path)) {
                        File::delete($path);
                    }
                    $img->delete();
                }

                $file = $request->file('product_image');

                $folder = public_path('uploads/products');
                if (! File::exists($folder)) {
                    File::makeDirectory($folder, 0777, true);
                }

                $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                $file->move($folder, $fileName);

                ProductImage::create([
                    'prod_id' => $product->id,
                    'file_name' => 'uploads/products/'.$fileName,
                    'status' => 1,
                ]);
            }

            $final = Product::with(['productImage', 'productLink'])->find($product->id);

            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully',
                'data' => $final,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // =========================================================
    // 6) DELETE PRODUCT   
    // ==========================================================
    public function delete($id)
    {
        try {
            $user = Auth::user();

            $product = Product::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (! $product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            // delete images from folder + db
            $images = ProductImage::where('prod_id', $product->id)->get();
            foreach ($images as $img) {
                $path = public_path($img->file_name);
                if (File::exists($path)) {
                    File::delete($path);
                }
                $img->delete();
            }

            // delete links
            ProductLink::where('prod_id', $product->id)->delete();

            // delete product
            $product->delete();

            return response()->json([
                'status' => true,
                'message' => 'Product deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
