<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Ruta home
Route::get('/', function () {
    return view('welcome');
});

// Admin panel para productos
Route::resource('admin/products', ProductController::class)->names('admin.products');


Route::get('/ping', function () {
    return 'pong';
});