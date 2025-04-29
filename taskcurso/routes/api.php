<?php

use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\FaqController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::resource('tasks', TaskController::class)->only(['index', 'store', 'update', 'destroy']);

Route::get('/ping', function () {
    return 'pong';
});

// Registro de usuarios
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Uso de Productos 
Route::get('/productos', [ProductController::class, 'apiIndex']);

// FAQ API
Route::get('/faqs', [FaqController::class, 'index']);
Route::post('/faqs', [FaqController::class, 'store']);
Route::put('/faqs/{id}', [FaqController::class, 'update']);
Route::delete('/faqs/{id}', [FaqController::class, 'destroy']);
Route::get('/faqs/categories', function () {
    return \App\Models\FaqCategory::all();
});
