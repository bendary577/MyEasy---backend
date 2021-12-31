<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['cors', 'json.response']], function () {
    Route::post('/register', 'AuthController@register');
    Route::post('/login', 'AuthController@login');
});

Route::middleware('auth:api')->group(function () {
    // Logout
    Route::post('/logout', 'AuthController@logout');

    // Category Routes
    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', 'CategoryController@index');
        Route::get('/categories-stores', 'CategoryController@indexWithStores');
        Route::get('/{id}', 'CategoryController@get');
        Route::post('/', 'CategoryController@create');
        Route::post('/{id}', 'CategoryController@update');
        Route::get('/delete/{id}', 'CategoryController@delete');
    });

    // Store Routes
    Route::group(['prefix' => 'stores'], function () {
        Route::get('/', 'StoreController@index');
        Route::get('/{category_id}', 'StoreController@indexByCategory');
        Route::get('/{id}', 'StoreController@get');
        Route::post('/{category_id}', 'StoreController@create');
        Route::post('/{id}', 'StoreController@update');
        Route::get('/delete/{id}', 'StoreController@delete');
    });

    // Product Routes
    Route::group(['prefix' => 'products'], function () {
        Route::get('/{store_id}', 'ProductController@getAll');
        Route::get('/{id}', 'ProductController@getOne');
        Route::post('/', 'ProductController@create');
        Route::post('/{id}', 'ProductController@update');
        Route::post('/delete/{id}', 'ProductController@delete');
    });

    // Invoice Routes
    Route::group(['prefix' => 'invoices'], function () {
        Route::get('/', 'InvoiceController@index');
        Route::get('/{id}', 'InvoiceController@get');
        Route::post('/', 'InvoiceController@create');
        Route::post('/{id}', 'InvoiceController@update');
        Route::get('/{id}', 'InvoiceController@delete');
    });

    // Order Routes
    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', 'OrderController@index');
        Route::get('/{id}', 'OrderController@get');
        Route::post('/{product_id}', 'OrderController@create');
        Route::put('/{id}', 'OrderController@update');
        Route::put('/{id}', 'OrderController@confirm');
        Route::post('/delete/{id}', 'OrderController@delete');
    });

    // Complaint Routes
    Route::group(['prefix' => 'complaint'], function () {
        Route::get('/', 'ComplaintController@getAll');
        Route::get('/{id}', 'ComplaintController@getOne');
        Route::get('/user', 'ComplaintController@get_user_complaint');
        Route::post('/', 'ComplaintController@create');
        Route::post('/edit/{id}', 'ComplaintController@update');
        Route::post('/delete/{id}', 'ComplaintController@delete');
    });

    // Cart Routes
    Route::group(['prefix' => 'cart'], function () {
        Route::get('/', 'CartController@getAll');
        Route::post('/', 'CartController@create');
        Route::post('/increase', 'CartController@increase');
        Route::post('/decrease', 'CartController@decrease');
        Route::put('/{id}', 'CartController@update');
        Route::post('/delete/{id}', 'CartController@destroy');
    });

    // Comment Routes
    Route::group(['prefix' => 'comment'], function () {
        Route::get('/', 'CommentController@getAll');
        Route::post('/{id}', 'CommentController@create');
        Route::get('/{id}', 'CommentController@getOne');
        Route::put('/{id}', 'CommentController@update');
        Route::post('/delete/{id}', 'CommentController@delete');
    });

    // Rating Routes
    Route::group(['prefix' => 'rating'], function () {
        Route::get('/user', 'RatingController@get_user_rating');
        Route::get('/product/{id}', 'RatingController@get_product_rating');
        Route::post('/', 'RatingController@create');
        Route::post('/edit', 'RatingController@update');
        Route::post('/delete/{id}', 'RatingController@delete');
    });

    // Search
    Route::post('/search', 'SearchController@search');

    Route::get('/user', function () {
        $roles = [];
        $user = User::first();

        foreach ($user->roles as $role) {
            array_push($roles, $role->name);
        }

        $data = [
            "name"  => $user->first_name . ' ' . $user->second_name,
            "email"  => $user->email,
            "phone"  => $user->phone_number,
            "roles" => $roles
        ];
        return $data;
    });
});
