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
use App\Http\Controllers\Admin\ReportAdminController;
use App\Http\Controllers\Admin\OrderController;





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

    // Reports admin web
    Route::resource('reports', ReportAdminController::class)->names('reports');
    Route::get('reports/metricas/calcular', [ReportAdminController::class, 'calcularMetricas'])
        ->name('reports.metricas');
    Route::get('reports/graficos/mostrar', [ReportAdminController::class, 'mostrarGraficos'])
        ->name('reports.graficos');
    Route::post('reports/filtros/fechas', [ReportAdminController::class, 'filtrarFechas'])
        ->name('reports.filtros');
    Route::post('reports/exportar/pdf', [ReportAdminController::class, 'exportarPdf'])
        ->name('reports.pdf');
    Route::get('reports/validacion/roles', [ReportAdminController::class, 'validarAccesosPorRol'])
        ->name('reports.roles');
    Route::get('reports/formatos/probar', [ReportAdminController::class, 'probarExportacionFormato'])
        ->name('reports.formatos');
    Route::get('reports/navegacion/vista', [ReportAdminController::class, 'estilizarVistaReportes'])
        ->name('reports.navegacion');

    Route::resource('faqs', FaqAdminController::class)->names('faqs');
    Route::resource('categories', CategoryAdminController::class)->names('categories');
    Route::resource('themes', ThemeAdminController::class)->names('themes');
    Route::resource('orders', OrderAdminController::class)->names('orders');
    //Route::resource('users', UserAdminController::class)->names('users');
    //Route::resource('roles', RoleAdminController::class)->names('roles');
    //Route::resource('permissions', PermissionAdminController::class)->names('permissions');
    //Route::resource('settings', SettingAdminController::class)->names('settings');
    //Route::resource('coupons', CouponAdminController::class)->names('coupons');
});



Route::get('/ping', function () {
    return 'pong';
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('orders', OrderController::class)->names('orders')->parameters(['orders' => 'pedido']);
    Route::put('orders/{pedido}/status', [OrderController::class, 'updateStatus'])
        ->name('orders.updateStatus');
});