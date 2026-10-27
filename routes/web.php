<?php

use Illuminate\Http\Request;
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

$catalogo = function (Request $request) {
    $tiendas = Tienda::query()->where('estado', true)->orderBy('nombre')->get();
    $categorias = Categoria::query()->where('estado', true)->orderBy('nombre_categoria')->get();

    $productos = Productos::query()
        ->with(['categoria', 'tienda'])
        ->where('estado', true)
        ->where('stock', '>', 0)
        ->whereHas('tienda', fn ($query) => $query->where('estado', true))
        ->when($request->filled('tienda'), fn ($query) => $query->where('id_tienda', $request->integer('tienda')))
        ->when($request->filled('categoria'), fn ($query) => $query->where('id_categoria', $request->integer('categoria')))
        ->when($request->filled('buscar'), function ($query) use ($request) {
            $buscar = trim((string) $request->input('buscar'));
            $query->where(function ($subquery) use ($buscar) {
                $subquery->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        })
        ->latest()
        ->get();

    return view('layouts.catalogo', compact('tiendas', 'categorias', 'productos'));
};

Route::get('/', $catalogo)->name('inicio');
Route::get('/catalogo', $catalogo)->name('catalogo');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

Route::resource('categoria', CategoriaController::class);
Route::resource('cliente', ClienteController::class);
Route::resource('facturas', FacturasController::class);
Route::resource('productos', ProductosController::class);
Route::resource('detalle_facturas', DetalleFacturaController::class);
Route::resource('tiendas', TiendaController::class);
