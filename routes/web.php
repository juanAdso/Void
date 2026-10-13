<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
Use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\FacturasController;
use App\Http\Controllers\DetalleFacturaController;
use App\Models\Productos;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
Route::resource('categoria', CategoriaController::class);
Route::resource('cliente', ClienteController::class);
Route::resource('facturas', FacturasController::class);
Route::resource('productos', ProductosController::class);
Route::resource('detalle_facturas', DetalleFacturaController::class);
Route::get('/consultas', function () { 
    return Productos::findorfail(2); });
