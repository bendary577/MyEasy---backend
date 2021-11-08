<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use Auth;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{
    /*
        getAll stores
        create store
        getOne store
        update store
        delete store
    */
    /* -------------------------------------------get all store ------------------------------------------------ */
    public function getAll()
    {
        if (!Auth::user()->can('gatAll store')) {
            return response(['Permission Denied']);
        }

        $stores = Store::paginate(10);
        return response([
            'message'   => 'Return All Stores',
            'data'      => $stores
        ], 200);
    }

    /* ------------------------------------- create an store -------------------------------------- */
    public function create(Request $request)
    {
        if (!Auth::user()->can('create store')) {
            return response(['Permission Denied']);
        }

        $data = $request->all();

        //validator or request validator
        $validator = Validator::make($data, [
            'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 'Validation Error');
        }

        $store = Store::create([
            'name'  => $data['name'],
            'user_id'  => Auth::user()->id,
            'category_id'  => $data['category_id']
        ]);
        return response(["message" => "store record created"], 201);
    }

    /* -------------------------------------get one store -------------------------------------- */
    public function getOne($id)
    {
        if (!Auth::user()->can('gat store')) {
            return response(['Permission Denied']);
        }

        if (Store::where('id', $id)->exists()) {
            $store = Store::where('id', $id)->get();
            return response([
                'message'   => 'Return One Store',
                'data'      => $store
            ], 200);
        } else {
            return response(["message" => "store not found"], 404);
        }
    }

    /* -------------------------------------update one store -------------------------------------- */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('update store')) {
            return response(['Permission Denied']);
        }

        if (Store::where('id', $id)->exists()) {
            $store = Store::find($id);
            $store->name = is_null($request->name) ? $store->name : $request->name;
            $store->save();

            return response(["message" => "store updated successfully"], 200);
        } else {
            return response(["message" => "store not found"], 404);
        }
    }

    /* -------------------------------------delete store -------------------------------------- */
    public function delete($id)
    {
        if (!Auth::user()->can('delete store')) {
            return response(['Permission Denied']);
        }

        if (Store::where('id', $id)->exists()) {
            $store = Store::find($id);
            $store->delete();
            return response(["message" => "store record deleted"], 202);
        } else {
            return response(["message" => "store not found"], 404);
        }
    }

}
