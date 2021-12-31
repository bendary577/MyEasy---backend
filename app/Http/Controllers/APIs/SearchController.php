<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Product;

class SearchController extends Controller
{
    public function search(Request $request){
        $keyword = $request->search;
        $stores = Store::search($keyword)->get();
        $products = Product::search($keyword)->get();
        if(sizeof($stores) > 0 || sizeof($products) > 0){
            return response()->json([
                'message' => "search results returned succeessfully",
                'stores' => $stores,
                'products' => $products
            ], 200);
        }
        return response()->json(['message' => "sorry, we can't find your search"], 200);
    }
}
