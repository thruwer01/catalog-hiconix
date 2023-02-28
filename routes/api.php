<?php

use App\Http\Controllers\v1\BrandController;
use App\Http\Controllers\v1\CategoryController;
use App\Http\Controllers\v1\ProductController;
use App\Http\Controllers\v1\SeriesController;
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

Route::middleware('auth_api')->group(function () {

    Route::controller(ProductController::class)->group(function () {
        Route::get('/products', 'index');
        Route::get('/products/{id}', 'show');
        Route::get('/products/{id}/sets', 'sets');
        Route::get('/products/{id}/stock', 'stock');
    });

    Route::controller(BrandController::class)->group(function () {
        Route::get('/brands', 'index');
        Route::get('/brands/{id}', 'show');
    });

    Route::controller(CategoryController::class)->group(function () {
        Route::get('/categories', 'index');
        Route::get('/categories/{id}', 'show');
    });

    Route::controller(SeriesController::class)->group(function () {
        Route::get('/series', 'index');
        Route::get('/series/{id}', 'show');
    });
    
});