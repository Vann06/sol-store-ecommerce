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

    // Reports admin web - for demo environments we allow public access so sidebar
    // links go directly to the reports page. Remove or re-enable auth middleware
    // for production as appropriate.
        Route::get('reports/metricas/calcular', [ReportAdminController::class, 'calcularMetricas'])
            ->name('reports.metricas');
        Route::get('reports/metricas', [ReportAdminController::class, 'metricas'])
            ->name('reports.metricas.view');
        Route::get('reports/graficos/mostrar', [ReportAdminController::class, 'mostrarGraficos'])
            ->name('reports.graficos');
        // Allow GET requests to the filtros endpoint so users can navigate directly
        // to the filters page without causing a 405 when visiting the URL.
        Route::get('reports/filtros/fechas', [ReportAdminController::class, 'filtrarFechas'])
            ->name('reports.filtros.view');
        Route::post('reports/filtros/fechas', [ReportAdminController::class, 'filtrarFechas'])
            ->name('reports.filtros');
        Route::post('reports/exportar/pdf', [ReportAdminController::class, 'exportarPdf'])
            ->name('reports.pdf');
        Route::get('reports/chart-image', [ReportAdminController::class, 'chartImage'])
            ->name('reports.chart.image');
        Route::get('reports/diagnostico-csrf', [ReportAdminController::class, 'diagnosticoCsrf'])
            ->name('reports.diagnostico');
        Route::get('reports/validacion/roles', [ReportAdminController::class, 'validarAccesosPorRol'])
            ->name('reports.roles');
        Route::get('reports/formatos/probar', [ReportAdminController::class, 'probarExportacionFormato'])
            ->name('reports.formatos');
        Route::get('reports/navegacion/vista', [ReportAdminController::class, 'estilizarVistaReportes'])
            ->name('reports.navegacion');

    // Register resource routes AFTER specific report routes to avoid /reports/{report}
    // from capturing named pages like /reports/metricas.
    Route::resource('reports', ReportAdminController::class)->names('reports')->except(['show']);

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

// Simple session-based auth routes for admin UI
use App\Http\Controllers\Auth\WebAuthController;
Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// Legacy order routes were previously registered here; the admin group above
// already uses `OrderAdminController`. To avoid route conflicts after the
// rebase we rely on the admin resource registered earlier. If legacy routes
// are required for backwards compatibility, they should be aliased explicitly
// or migrated to use the admin controller.