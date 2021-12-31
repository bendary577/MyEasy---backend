<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StoreProduct;
use App\Models\File;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /*
        getAll products
        getOne product
        create product
        update product
        delete product
    */
    /* -------------------------------------------get all store products ------------------------------------------------ */
    public function index($store_id)
    {
        if (!Auth::user()->can('getAll product')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        $products = Redis::get('products');
        if(isset($products)) {
            $products = json_decode($products, FALSE);
        }else{
            $store = Store::where('id', $store_id)->with('storeProducts')->first();
            $products = $store->storeProducts;
            Redis::set('products', $products);
        }
        return response()->json([
            'message' => trans('product.products.returned.successfully'),
            'data' => $products,
        ], 200);
    }

    /* -------------------------------------get store product details -------------------------------------- */
    public function get($id)
    {
        if (!Auth::user()->can('get product')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        if (Product::where('id', $id)->exists()) {
            $product = Redis::get('product');
            if(isset($product)) {
                $product = json_decode($product, FALSE);
            }else{
                $product = StoreProduct::where('id', $id)->with('images', 'comments', 'ratings')->get();
                Redis::set('product', $product);
            }
            return response()->json([
                'message' => trans('product.product.returned.successfully'),
                'data' => $product,
            ], 200);
        } else {
            return response()->json(["message" =>  trans('product.not.found')], 404);
        }
    }

    /* ------------------------------------- create a store product -------------------------------------- */
    public function create(Request $request)
    {
        if (!Auth::user()->can('create product')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'description' => 'required|max:255',
            'price' => 'required',
            'available_number' => 'required|integer',
            'status' => 'required|in:new,used',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        $product = new Product;
        $product->name = $request['name'];
        $product->price = $request['price'];
        $product->save();
        $store_product = new StoreProduct();
        $store_product->code = $store_product->generateCode();
        $store_product->description = $request['description'];
        $store_product->available_number  = $request['available_number'];
        $store_product->status = $request['status'];
        $store_product->product()->save($product);
        if($request->hasfile('product_images')){
            foreach($request->file('product_images') as $product_image_file){
                $product_image_file_name = $product_image_file->getClientOriginalName();
                $product_image_size = $product_image_file->getClientSize();
                $product_image_extention = $product_image_file->extension();
                $product_image_path = '/stores/'.$store->code.'/products/'.$store_product->code.'/images/';
                //make new file
                $file = new File();
                $file->name = $product_image_file_name;
                $file->path = $product_image_path;
                $file->extention = $product_image_extention;
                $file->size = $product_image_size;
                $file->save();
                $product_image = new ProductImage();
                $product_image->file()->save($file);
                $product_image->storeProduct()->associate($store_product)->save();
                $product_image_file->move(public_path().$product_image_path, $product_image_file_name);
            }
        }
        return response(['message' => trans('product.created.successfully')], 201);
    }

    /* -------------------------------------update one product -------------------------------------- */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('update product')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'min:8|max:255',
            'price' => 'float',
            'description' => 'max:255',
            'available_number' => 'integer',
            'status' => 'in:new,used',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        if (Product::where('id', $id)->exists()) {
            $product = Product::find($id);
            if($request['name']){
                $product->name = $request['name'];
            }
            if($request['price']){
                $product->price = $request['price'];
            }
            if($request['description']){
                $product->product->description = $request['description'];
            }
            if($request['available_number']){
                $product->product->available_number = $request['available_number'];
            }
            if($request['status']){
                $product->product->status = $request['status'];
            }
            if($request->hasfile('product_images')){
                foreach($request->file('product_images') as $product_image_file){
                    $product_image_file_name = $product_image_file->getClientOriginalName();
                    $product_image_size = $product_image_file->getClientSize();
                    $product_image_extention = $product_image_file->extension();
                    $product_image_path = '/stores/'.$store->code.'/products/'.$store_product->code.'/images/';
                    //make new file
                    $file = new File();
                    $file->name = $product_image_file_name;
                    $file->path = $product_image_path;
                    $file->extention = $product_image_extention;
                    $file->size = $product_image_size;
                    $file->save();
                    $product_image = new ProductImage();
                    $product_image->file()->save($file);
                    $product_image->storeProduct()->associate($store_product)->save();
                    $product_image_file->move(public_path().$product_image_path, $product_image_file_name);
                }
            }
            $product->save();
            return response(["message" => trans('product.updated.successfully')], 200);
        } else {
            return response(["message" => trans('product.not.found')], 404);
        }
    }

    /* -------------------------------------delete product -------------------------------------- */
    public function delete($id)
    {
        if (!Auth::user()->can('delete product')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        if (Product::where('id', $id)->exists()) {
            $product = Product::find($id);
            $product->delete();
            return response()->json(["message" => trans('product.deleted.successfully')], 202);
        } else {
            return response()->json(["message" => trans('product.not.found')], 404);
        }
    }
}
