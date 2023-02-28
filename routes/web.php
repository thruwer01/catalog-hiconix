<?php

use App\Http\Controllers\GetManagerAmoController;
use App\Http\Controllers\SyncPriceController;
use App\Http\Controllers\SyncStockController;
use Illuminate\Support\Facades\Route;

// Platform > System > Sync Price with 1C
Route::match(['get', 'post'],'/sync_price', [SyncPriceController::class, 'sync'])
    ->name('platform.sync.price')
    ->middleware('guest');

// Platform > System > Sync Stock with 1C
Route::match(['get', 'post'],'/sync_stock', [SyncStockController::class, 'sync'])
    ->name('platform.sync.stock')
    ->middleware('guest');

// Get manager amo id from site request
Route::get('/manager/get_by_number', [GetManagerAmoController::class, 'get'])->middleware('guest');