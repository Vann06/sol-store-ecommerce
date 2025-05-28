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


// Autenticación (sin sesión)
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Productos públicos
Route::get('/productos', [ProductController::class, 'apiIndex']);
Route::get('/productos/recientes', [ProductController::class, 'productosRecientes']);
Route::get('/productos/{id}', [ProductController::class, 'apiShow']);

// FAQ API
Route::get('/faqs', [FaqController::class, 'index']);
Route::post('/faqs', [FaqController::class, 'store']);
Route::get('/faqs/{id}', [FaqController::class, 'show']);
Route::put('/faqs/{id}', [FaqController::class, 'update']);
Route::delete('/faqs/{id}', [FaqController::class, 'destroy']);
Route::get('/faqs/categories', function () {
    return \App\Models\FaqCategory::all();
});

// Rutas del carrito SIN middleware (el controller maneja la autenticación)
Route::prefix('carrito')->group(function () {
    Route::get('/', [CarritoController::class, 'getCarrito']);
    Route::post('/agregar', [CarritoController::class, 'agregarProducto']);
    Route::put('/actualizar/{detalle_id}', [CarritoController::class, 'actualizarCantidad']);
    Route::delete('/eliminar/{detalle_id}', [CarritoController::class, 'eliminarProducto']);
    Route::delete('/vaciar', [CarritoController::class, 'vaciarCarrito']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Categorias de un producto
Route::get('/categorias', [CategoryController::class, 'apiIndex']);
//Tematicas de un producto 
Route::get('/tematicas', [ThemeController::class, 'apiIndex']);

// Rutas autenticadas obligatorias
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/auth/check', [AuthController::class, 'checkAuth']);
    
    // Transferir carrito al hacer login
    Route::post('/carrito/transferir', [CarritoController::class, 'transferirCarritoLogin']);
    
    // Rutas que requieren autenticación (checkout, etc.)
    Route::prefix('checkout')->group(function () {
        // Aquí irán las rutas del checkout
    });
});


// Test endpoint
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

