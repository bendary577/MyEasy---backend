<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;

class RatingController extends Controller
{
    /*
        user ratings
        product rating
        create rating
        getOne rating
        update rating
        delete rating
    */
    public function get_user_rating()
    {
        if (!Auth::user()->can('user rating')) {
            return response(['Permission Denied']);
        }

        $rating = Rating::where('user_id', Auth::user()->id)->paginate(10);
        return response([
            'message'   => 'User Ratings',
            'data'      => $rating
        ], 200);
    }

    public function get_product_rating($id)
    {
        if (!Auth::user()->can('product rating')) {
            return response(['Permission Denied']);
        }
        $rating = Rating::where('product_id', $id)->paginate(10);
        return response([
            'message'   => 'Product Ratings',
            'data'      => $rating
        ], 200);
    }

    public function Create(Request $request)
    {
        if (!Auth::user()->can('create rating')) {
            return response(['Permission Denied']);
        }

        $data = $request->all();
        $avg = 0;

        //validator or request validator
        $validator = Validator::make($data, [
            'rate' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 'Validation Error');
        }

        if ($data['rate'] > 5 || $data['rate'] < 0) {
            return response(['message' => 'Rating Must Be Between 0 and 5']);
        }

        $product = Product::find($data['product_id']);
        if (!$product) {
            return response(['message' => 'Product NotFound']);
        }
        $rating = Rating::create([
            'user_id'   => Auth::user()->id,
            'product_id' => $product->id,
            'rate'      => $data['rate']
        ]);

        $num = 1 + $product->ratings_number;
        $product->ratings_number = $num;
        $product->save();

        $rates = Rating::where('product_id', $data['product_id'])->get();
        foreach ($rates as $rate) {
            $avg = $avg + $rate->rate;
        }
        $product->rating = $avg / $num;
        $product->save();

        return response(["message" => "Rating record created"], 201);
    }

    /*
    public function getOne($id)
    {
        if (Rating::where('id', $id)->exists()) {
            $rating = Rating::where('id', $id)->get()->toJson();
            return response($rating, 200);
        } else {
            return response()->json(["message" => "Rating not found"], 404);
        }
    }
    */

    public function update(Request $request)
    {
        if (!Auth::user()->can('update rating')) {
            return response(['Permission Denied']);
        }

        $id = $request->id;
        $avg = 0;
        // return 0;
        if (Rating::where('id', $id)->exists()) {
            $rating = Rating::find($id);
            $rating->rate = is_null($request->rate) ? $rating->rate : $request->rate;
            $rating->save();

            $rates = Rating::where('product_id', $rating->product_id)->get();
            $product = Product::find($rating->product_id);
            $num = $product->ratings_number;
            foreach ($rates as $rate) {
                $avg = $avg + $rate->rate;
            }
            $product->rating = $avg / $num;
            $product->save();

            return response(["message" => "Rating updated successfully"], 200);
        } else {
            return response(["message" => "Rating not found"], 404);
        }
    }

    public function delete($id)
    {
        if (!Auth::user()->can('delete rating')) {
            return response(['Permission Denied']);
        }

        $avg = 0;
        if (Rating::where('id', $id)->exists()) {
            $rating = Rating::find($id);
            $rating->delete();

            $rates = Rating::where('product_id', $rating->product_id)->get();
            $product = Product::find($rating->product_id);
            $num = $product->ratings_number - 1;
            $product->ratings_number = $num;

            foreach ($rates as $rate) {
                $avg = $avg + $rate->rate;
            }
            $product->rating = $avg / $num;
            $product->save();

            return response(["message" => "Rating record deleted"], 202);
        } else {
            return response(["message" => "Rating not found"], 404);
        }
    }
}
