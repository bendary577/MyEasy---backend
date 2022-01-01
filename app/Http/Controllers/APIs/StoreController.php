<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\CompanyStore;
use App\Models\SellerStore;
use App\Models\Company;
use App\Models\Seller;
use App\Models\File;
use App\Models\Logo;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redis;

class StoreController extends Controller
{
    /*
        getAll stores
        getAll stores by category
        create store
        update store
        delete store
    */

    /* -------------------------------------------get all store ------------------------------------------------ */
    public function index()
    {
        if (!Auth::user()->can('get_all_stores')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        $stores = Redis::get('stores');
        if(isset($stores)) {
            $stores = json_decode($stores, FALSE);
        }else{
            $stores = Store::with('storeProducts')->paginate(10);
            Redis::set('stores', $stores);
        }
        return response()->json([
            'message'   => trans('stores.stores.returned.successfully'),
            'data'      => $stores
        ], 200);
    }

    /* ------------------------------------------- get all stores by category ------------------------------------------------ */
    public function getStoresByCategory($category_id)
    {
        if (!Auth::user()->can('get_all_stores_by_category')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        $stores = Redis::get('stores');
        if(isset($stores)) {
            $stores = json_decode($stores, FALSE);
        }else{
            $stores = Store::where('category_id', $category_id)->paginate(10);
            Redis::set('stores', $stores);
        }
        return response()->json([
            'message'   => trans('stores.stores.returned.successfully'),
            'data'      => $stores
        ], 200);
    }

    /* -------------------------------------get one store -------------------------------------- */
    public function get($id)
    {
        if (!Auth::user()->can('get_store_details')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        if (Store::where('id', $id)->exists()) {
            $store = Redis::get('store');
            if(isset($store)) {
                $store = json_decode($store, FALSE);
            }else{
                $store = Store::where('id', $id)->with('storeProducts', 'logo')->get();
                Redis::set('store', $store);
            }
            return response()->json([
                'message'   => trans('store.store.returned.successfully'),
                'data'      => $store
            ], 200);
        } else {
            return response()->json(["message" => trans('store.not.found')], 404);
        }
    }

    /* ------------------------------------- create an store -------------------------------------- */
    public function create(Request $request, $category_id)
    {
        if (!Auth::user()->can('create_store')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:8|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        $category = Category::where('id', $category_id)->first();
        $store = new Store();
        $store->title = $request['title'];
        $store->code = $store->generateCode();
        $store->customers_number = 0;
        $store->orders_number = 0;
        $store->save();
        if(Auth::user()->getHasCompanyProfileAttribute()){
            $company_store = new CompanyStore();
            $company_store->store()->save($store);
            Auth::user()->profile()->store()->save($company_store);
        }else if(Auth::user()->getHasSellerProfileAttribute()){
            $seller_store = new SellerStore();
            $seller_store->store()->save($store);
            Auth::user()->profile()->store()->save($seller_store);
        }
        return response(["message" => trans('store.created.successfully')], 201);
    }

    /* -------------------------------------update one store -------------------------------------- */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('update_store')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'min:8|max:255',
            'logo' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048|dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        if (Store::where('id', $id)->exists()) {
            if($request['title']){
                $store->title = $request['title'];
            }
            if($request->hasfile('logo')){
                //get request file data
                $logo_file = $request->file('logo');
                $logo_file_name = $logo_file->getClientOriginalName();
                $logo_file_size = $logo_file->getClientSize();
                $logo_file_extention = $logo_file->extension();
                $logo_file_path = '/stores/'.$store->code.'/logo/';
                //make new file
                $file = new File();
                $file->name = $logo_file_name;
                $file->path = $logo_file_path;
                $file->extention = $logo_file_extention;
                $file->size = $logo_file_size;
                $logo = new Logo();
                $logo->file()->save($file);
                $logo->store()->associate($store)->save();
                $logo_file->move(public_path().$logo_file_path, $logo_file_name);
            }
            $store->save();
            return response(["message" => trans('store.updated.successfully')], 200);
        } else {
            return response(["message" => trans('store.not.found')], 404);
        }
    }

    /* -------------------------------------delete store -------------------------------------- */
    public function delete($id)
    {
        if (!Auth::user()->can('delete_store')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        if (Store::where('id', $id)->exists()) {
            $store = Store::find($id);
            $store->delete();
            return response()->json(["message" => trans('store.deleted.successfully')], 200);
        } else {
            return response()->json(["message" => trans('store.not.found')], 404);
        }
    }

}
