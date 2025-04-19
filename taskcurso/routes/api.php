<?php

use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;


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
