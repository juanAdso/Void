<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\FacturasController;
use App\Http\Controllers\DetalleFacturaController;
use App\Http\Controllers\TiendaController;
use App\Models\Categoria;
use App\Models\Productos;
use App\Models\Tienda;

Route::get('/', function () {
    $tiendas = Tienda::where('estado', true)->get();
    $categorias = Categoria::where('estado', true)->get();
    $productos = Productos::with(['categoria', 'tienda'])
        ->where('estado', true)
        ->where('stock', '>', 0)
        ->get();

    return view('layouts.catalogo', compact('tiendas', 'categorias', 'productos'));
})->name('inicio');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

Route::resource('categoria', CategoriaController::class);
Route::resource('cliente', ClienteController::class);
Route::resource('facturas', FacturasController::class);
Route::resource('productos', ProductosController::class);
Route::resource('detalle_facturas', DetalleFacturaController::class);
Route::resource('tiendas', TiendaController::class);

Route::get('/consultas', function () {
    return Productos::findOrFail(2);
});
