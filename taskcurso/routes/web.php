<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\FaqAdminController;

// Ruta home
Route::get('/', function () {
    return view('welcome');
});

// Admin panel 
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', ProductController::class)
        ->parameters(['products' => 'product'])
        ->names('products');

    Route::resource('faqs', FaqAdminController::class)->names('faqs');
});



Route::get('/ping', function () {
    return 'pong';
});

