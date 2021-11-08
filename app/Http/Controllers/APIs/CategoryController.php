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
        getstore category
        getOne category
        create category
        update category
        delete category
    */
    public function getAll()
    {
        if (!Auth::user()->can('getAll category')) {
            return response(['Permission Denied']);
        }
        $categories = Category::paginate(10);
        return response([
            'message'   => 'success returned categories',
            'data'      => $categories,
        ], 200);
    }

    public function category_store()
    {
        if (!Auth::user()->can('getstore category')) {
            return response(['Permission Denied']);
        }
        $categories = Category::with('store')->paginate(10);
        return response([
            'message'   => 'success returned categories',
            'data'      => $categories,
        ], 200);
    }

    public function create(Request $request)
    {
        if (!Auth::user()->can('create category')) {
            return response(['Permission Denied']);
        }
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required|max:255|unique:categories',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 'Validation Error');
        }

        Category::create([
            'name'  => $data['name'],
        ]);
        return response(['message' => 'Category record created'], 201);
    }

    public function getOne($id)
    {
        if (!Auth::user()->can('get category')) {
            return response(['Permission Denied']);
        }
        if (Category::where('id', $id)->exists()) {
            $category = Category::where('id', $id)->get();
            return response([
                'message'   => 'One Category Return',
                'data'      => $category
            ], 200);
        } else {
            return response(["message" => "Category not found"], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('update category')) {
            return response(['Permission Denied']);
        }
        // return $request;
        if (Category::where('id', $id)->exists()) {
            $category = Category::find($id);
            $category->name = $request->name;
            $category->save();

            return response()->json(["message" => "Category updated successfully"], 200);
        } else {
            return response()->json(["message" => "Category not found"], 404);
        }
    }

    public function delete($id)
    {
        if (!Auth::user()->can('delete category')) {
            return response(['Permission Denied']);
        }

        if (Category::where('id', $id)->exists()) {
            $category = Category::find($id);
            $category->delete();
            return response(["message" => "Category record deleted"], 202);
        } else {
            return response(["message" => "Category not found"], 404);
        }
    }
}
