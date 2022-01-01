<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['cors', 'json.response']], function () {
    Route::post('/register', 'AuthController@register');
    Route::post('/login', 'AuthController@login');
    Route::post('/{id}/activate-user', 'AuthController@activateUser');
    Route::post('/send-code', 'AuthController@sendForgotPasswordCode');
    Route::post('/check-code', 'AuthController@checkForgotPasswordCode');
    Route::post('/reset-password', 'AuthController@resetPassword');
});

Route::middleware('auth:api')->group(function () {
    //User Routes
    Route::group(['prefix' => 'user'], function () {
        Route::get('/', 'UserController@get');
        Route::post('/', 'UserController@update');
        Route::post('/logout', 'UserController@logout');
    });

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
        Route::get('/{category_id}', 'StoreController@getStoresByCategory');
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
        Route::get('/', 'ComplaintController@index');
        Route::get('/user/{id}', 'ComplaintController@getUserComplaints');
        Route::get('/{id}', 'ComplaintController@get');
        Route::post('/', 'ComplaintController@create');
        Route::post('/edit/{id}', 'ComplaintController@update');
        Route::get('/delete/{id}', 'ComplaintController@delete');
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

});
