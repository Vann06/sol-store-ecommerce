<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\FaqAdminController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\ThemeAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\RoleAdminController;
use App\Http\Controllers\Admin\PermissionAdminController;
use App\Http\Controllers\Admin\SettingAdminController;
use App\Http\Controllers\Admin\CouponAdminController;
use App\Http\Controllers\Admin\InventarioAdminController;




// Ruta home
Route::get('/', function () {
    return view('welcome');
});

// Admin panel 
Route::prefix('admin')->name('admin.')->group(function () {

    Route::resource('products', ProductController::class)
        ->parameters(['products' => 'product'])
        ->names('products');

    // Inventario admin web
    Route::resource('inventario', InventarioAdminController::class)
        ->names('inventario');

    Route::resource('faqs', FaqAdminController::class)->names('faqs');
    Route::resource('categories', CategoryAdminController::class)->names('categories');
    Route::resource('themes', ThemeAdminController::class)->names('themes');
    Route::resource('users', UserAdminController::class)->names('users');
    Route::resource('orders', OrderAdminController::class)->names('orders');
    Route::resource('roles', RoleAdminController::class)->names('roles');
    Route::resource('permissions', PermissionAdminController::class)->names('permissions');
    Route::resource('settings', SettingAdminController::class)->names('settings');
    Route::resource('coupons', CouponAdminController::class)->names('coupons');
});



Route::get('/ping', function () {
    return 'pong';
});

