<?php

use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\CarritoController;




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
Route::get('/faqs/{id}', [FaqController::class, 'show']);
Route::put('/faqs/{id}', [FaqController::class, 'update']);
Route::delete('/faqs/{id}', [FaqController::class, 'destroy']);
Route::get('/faqs/categories', function () {
    return \App\Models\FaqCategory::all();
});




Route::get('/productos/recientes', [ProductController::class, 'productosRecientes']);


Route::get('/productos/{id}', [ProductController::class, 'apiShow']);

// Categorias 
Route::get('/categorias', [CategoryController::class, 'apiIndex']);

// Tematicas
Route::get('/tematicas', [ThemeController::class, 'apiIndex']);

Route::middleware('auth:sanctum')->group(function () {
    // Rutas para el carrito de compras
    Route::prefix('carrito')->group(function () {
        Route::get('/', [CarritoController::class, 'getCarrito']);
        Route::post('/agregar', [CarritoController::class, 'agregarProducto']);
        Route::put('/actualizar/{detalle_id}', [CarritoController::class, 'actualizarCantidad']);
        Route::delete('/eliminar/{detalle_id}', [CarritoController::class, 'eliminarProducto']);
        Route::delete('/vaciar', [CarritoController::class, 'vaciarCarrito']);
    });
});

