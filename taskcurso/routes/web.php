<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\FaqAdminController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\ThemeAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\ReportAdminController;
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
    Route::resource('orders', OrderAdminController::class)->names('orders');
    // Reportes
    Route::get('reports', [ReportAdminController::class, 'index'])->name('reports.index');
    Route::get('reports/metricas', [ReportAdminController::class, 'metricas'])->name('reports.metricas.view');
    Route::get('reports/graficos', [ReportAdminController::class, 'mostrarGraficos'])->name('reports.graficos');
    // Unificar filtros en una sola ruta GET para evitar 419 por CSRF en navegaciones simples
    Route::get('reports/filtros', [ReportAdminController::class, 'filtrarFechas'])->name('reports.filtros.view');
    // Mantener POST opcional (exportaciones futuras) pero usar mismo método
    Route::post('reports/filtros', [ReportAdminController::class, 'filtrarFechas'])->name('reports.filtros');
    // Exportaciones ahora con GET para evitar CSRF (se pueden llamar directamente desde enlaces)
    Route::get('reports/pdf', [ReportAdminController::class, 'exportarPdf'])->name('reports.pdf');
    Route::get('reports/excel', [ReportAdminController::class, 'exportarExcel'])->name('reports.excel');
    Route::get('reports/create', [ReportAdminController::class, 'create'])->name('reports.create');
    Route::get('reports/navegacion', [ReportAdminController::class, 'estilizarVistaReportes'])->name('reports.navegacion');
    //Route::resource('users', UserAdminController::class)->names('users');
    //Route::resource('roles', RoleAdminController::class)->names('roles');
    //Route::resource('permissions', PermissionAdminController::class)->names('permissions');
    //Route::resource('settings', SettingAdminController::class)->names('settings');
    //Route::resource('coupons', CouponAdminController::class)->names('coupons');
});



Route::get('/ping', function () {
    return 'pong';
});

