<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;

class CartController extends Controller
{
    /*
        getAll carts
        create cart
        increase cart
        decrease cart
        delete cart
    */
    public function getAll()
    {
        if (!Auth::user()->can('getAll cart')) {
            return response(['Permission Denied']);
        }

        $arr = [];
        $total = 0;

        $carts = Cart::where('user_id', Auth::user()->id)->paginate(10);

        foreach ($carts as $cart) {
            $product = Product::find($cart->product_id);
            array_push($arr, $product);
            $total = $total + $cart->total;
        }

        return response([
            'message'   => 'Your Cart',
            'data'      => $carts,
            'products'  => $arr,
            'total'     => $total
        ], 200);
    }


    public function create(Request $request)
    {
        if (!Auth::user()->can('create cart')) {
            return response(['Permission Denied']);
        }

        if (Cart::where('product_id', $request->product_id)->exists()) {
            return response(['message' => 'Product Already In Cart'], 402);
        }

        $data = $request->all();
        $validator = Validator::make($data, [
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 'Validation Error');
        }

        $product =  Product::where('id', $data['product_id'])->first();
        if (!$product) {
            return response(['message' => 'Product Not Found'], 404);
        }

        $cart = Cart::create([
            'user_id'   => Auth::user()->id,
            'product_id' => $product->id,
            'quantity'  => 1,
            'price'     => $product->price,
            'total'     => $product->price,
            'state'     =>  'pending'
        ]);
        return response(["message" => "Cart record created"], 201);
    }

    public function increase(Request $request)
    {
        if (!Auth::user()->can('increase cart')) {
            return response(['Permission Denied']);
        }

        $id = $request->product_id;

        if (!Cart::where('product_id', $id)->exists()) {
            return response(['message' => 'Product Is not in Cart'], 404);
        }
        $cart = Cart::where('product_id', $id)->first();
        $qty = $cart->quantity + 1;
        $total = $qty * $cart->price;

        $cart->quantity = $qty;
        $cart->total    = $total;
        $cart->save();
        return response(['message' => 'Increase']);
    }

    public function decrease(Request $request)
    {
        if (!Auth::user()->can('decrease cart')) {
            return response(['Permission Denied']);
        }

        $id = $request->product_id;

        if (!Cart::where('product_id', $id)->exists()) {
            return response(['message' => 'Product Is not in Cart'], 404);
        }

        $cart = Cart::where('product_id', $id)->first();
        $qty = $cart->quantity - 1;
        $total = $qty * $cart->price;
        $cart->quantity = $qty;
        $cart->total    = $total;
        $cart->save();

        if ($qty == 0) {
            Cart::find($cart->id)->delete();
            return response(['message' => 'Delete Cart']);
        }

        return response(['message' => 'decrease']);
    }

    /*
    public function update($request, $product)
    {
        if (Cart::where('product', $product)->exists()) {
            $cart = cart::query()->where('product', $product);
            $cart->num = is_null($request->num) ? $cart->num : $request->num;
            $cart->save();

            return response()->json(["message" => "Cart updated successfully"], 200);
        } else {
            return response()->json(["message" => "Cart not found"], 404);
        }
    }
    */

    public function destroy($id)
    {
        if (!Auth::user()->can('delete cart')) {
            return response(['Permission Denied']);
        }

        if (Cart::where('id', $id)->exists()) {
            $cart = Cart::find($id);
            $cart->delete();
            return response(["message" => "Cart record deleted"], 202);
        } else {
            return response(["message" => "Cart not found"], 404);
        }
    }
}
