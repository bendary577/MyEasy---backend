<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /*
        getAll categories
        getAll categories with stores
        getOne category
        create category
        update category
        delete category
    */

    /* -------------------------------------getAll categories -------------------------------------- */
    public function index()
    {
        if (!Auth::user()->can('get_all_categories')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        $cached_categories = Redis::get('categories');
        if(isset($cached_categories)) {
            $categories = json_decode($cached_categories, FALSE);
            return response()->json([
                'message' => trans('category.categories.returned.successfully'),
                'data' => $cached_categories,
            ], 200);
        }else{
            $categories = Category::all();
            Redis::set('categories', $categories);
            return response()->json([
                'message'   => trans('category.categories.returned.successfully'),
                'data'      => $categories
            ], 200);
        }
    }

    /* -------------------------------------getAll categories -------------------------------------- */
    public function indexWithStores()
    {
        if (!Auth::user()->can('get_categories_with_stores')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        $categories_with_stores = Redis::get('categories_with_stores');
        if(isset($categories_with_stores)) {
            $categories_with_stores = json_decode($categories_with_stores, FALSE);
        }else{
            $categories_with_stores = Category::with('stores')->get();
            Redis::set('categories_with_stores', $categories_with_stores);
        }
        return response()->json([
            'message'   => trans('category.categories.returned.successfully'),
            'data'      => $categories_with_stores
        ], 200);
    }

    /* -------------------------------------update one order -------------------------------------- */
    public function get($id)
    {
        if (!Auth::user()->can('get_category_details')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        if (Category::where('id', $id)->exists()) {
            $category = Redis::get('category');
            if(isset($category)) {
                $category = json_decode($category, FALSE);
            }else{
                $category = Category::where('id', $id)->with('stores')->first();
                Redis::set('category', $category);
            }
            return response()->json([
                'message' => trans('category.category.returned.successfully'),
                'data' => $category,
            ], 200);
        } else {
            return response(["message" => trans('category.not.found')], 404);
        }
    }

    /* -------------------------------------update one order -------------------------------------- */
    public function create(Request $request)
    {
        if (!Auth::user()->can('create_category')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:categories',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        $category = new Category();
        $category->name = $request['name'];
        $category->save();
        return response()->json(['message' => trans('category.category.created.successfully')], 201);
    }


    /* -------------------------------------update one order -------------------------------------- */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('update_category')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:categories',
        ]);
        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }
        if(Category::where('id', $id)->exists()){
            $category = Category::find($id);
            if( $request['name']){
                $category->name = $request['name'];
            }
            $category->save();
            return response(['message' => trans('category.category.updated.successfully')], 201);
        } else {
            return response()->json(["message" => trans('category.not.found')], 404);
        }
    }

    /* -------------------------------------update one order -------------------------------------- */
    public function delete($id)
    {
        if (!Auth::user()->can('delete_category')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        if (Category::where('id', $id)->exists()) {
            $category = Category::find($id);
            $category->delete();
            return response(["message" => trans('category.category.deleted.successfully')], 202);
        } else {
            return response(["message" => trans('category.not.found')], 404);
        }
    }
}
