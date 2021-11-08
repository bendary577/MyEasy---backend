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
    Route::group(['prefix' => 'category'], function () {
        Route::get('/', 'CategoryController@getAll');
        Route::get('/{id}', 'CategoryController@getOne');
        Route::get('with/stores', 'CategoryController@category_store');
        Route::post('/', 'CategoryController@create');
        Route::post('/{id}', 'CategoryController@update');
        Route::post('/delete/{id}', 'CategoryController@delete');
    });

    // Store Routes
    Route::group(['prefix' => 'store'], function () {
        Route::get('/', 'StoreController@getAll');
        Route::get('/{id}', 'StoreController@getOne');
        Route::post('/', 'StoreController@create');
        Route::post('/{id}', 'StoreController@update');
        Route::post('/delete/{id}', 'StoreController@delete');
    });

    // Product Routes
    Route::group(['prefix' => 'product'], function () {
        Route::get('/', 'ProductController@getAll');
        Route::get('/{id}', 'ProductController@getOne');
        Route::get('/store/{id}', 'ProductController@get_product_store');
        Route::post('/', 'ProductController@create');
        Route::post('/{id}', 'ProductController@update');
        Route::post('/delete/{id}', 'ProductController@delete');
    });

    // Invoice Routes
    Route::group(['prefix' => 'invoice'], function () {
        Route::get('/', 'InvoiceController@getAll');
        Route::get('/user', 'InvoiceController@get_invoice_user');
        Route::get('/{id}', 'InvoiceController@getOne');
        Route::post('/', 'InvoiceController@create');
        Route::post('/{id}', 'InvoiceController@update');
        Route::post('/delete/{id}', 'InvoiceController@delete');
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

    // Order Routes
    Route::group(['prefix' => 'order'], function () {
        Route::get('/', 'OrderController@getAll');
        Route::get('/{id}', 'OrderController@get_order');
        Route::post('/', 'OrderController@create');
        Route::put('/{id}', 'OrderController@update');
        Route::post('/delete/{id}', 'OrderController@delete');
    });

    // Comment Routes
    Route::group(['prefix' => 'comment'], function () {
        Route::get('/', 'OrderController@getAll');
        Route::post('/{id}', 'OrderController@create');
        Route::get('/{id}', 'OrderController@getOne');
        Route::put('/{id}', 'OrderController@update');
        Route::post('/delete/{id}', 'OrderController@delete');
    });

    // Rating Routes
    Route::group(['prefix' => 'rating'], function () {
        Route::get('/user', 'RatingController@get_user_rating');
        Route::get('/product/{id}', 'RatingController@get_product_rating');
        Route::post('/', 'RatingController@create');
        Route::post('/edit', 'RatingController@update');
        Route::post('/delete/{id}', 'RatingController@delete');
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
