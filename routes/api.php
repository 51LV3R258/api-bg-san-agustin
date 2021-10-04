<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1.0.0'], function () {
    Route::apiResource('product', 'ProductController');
    Route::apiResource('tag', 'TagController', [
        'except' => ['destroy']
    ]);
    Route::apiResource('unit', 'UnitController', [
        'except' => ['destroy']
    ]);

    Route::group(['prefix' => 'search'], function () {
        Route::get('product', 'ProductController@search');
    });
});

Route::group(['prefix' => 'v1.0.1'], function () {
    Route::apiResource('products', 'ProductController');
    Route::apiResource('tags', 'TagController', [
        'except' => ['destroy']
    ]);
    Route::apiResource('units', 'UnitController', [
        'except' => ['destroy']
    ]);

    Route::group(['prefix' => 'search'], function () {
        Route::get('products', 'ProductController@search');
    });
});