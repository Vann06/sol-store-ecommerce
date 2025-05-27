<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductoController;

// Autenticación (sin sesión)
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Productos públicos
Route::get('/productos', [ProductoController::class, 'index']);
Route::get('/productos/{id}', [ProductoController::class, 'show']);

// Test endpoint
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

// Rutas del carrito SIN middleware (el controller maneja la autenticación)
Route::prefix('carrito')->group(function () {
    Route::get('/', [CarritoController::class, 'getCarrito']);
    Route::post('/agregar', [CarritoController::class, 'agregarProducto']);
    Route::put('/actualizar/{detalle_id}', [CarritoController::class, 'actualizarCantidad']);
    Route::delete('/eliminar/{detalle_id}', [CarritoController::class, 'eliminarProducto']);
    Route::delete('/vaciar', [CarritoController::class, 'vaciarCarrito']);
});

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