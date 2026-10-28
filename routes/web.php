<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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
    $categorias = Categoria::query()->where('estado', true)->orderBy('nombre')->get();

    $productos = Productos::query()
        ->with('categoria')
        ->where('productos.estado', true)
        ->where('productos.stock', '>', 0)
        ->when($request->filled('categoria'), fn ($query) => $query->where('productos.id_categoria', $request->integer('categoria')))
        ->when($request->filled('buscar'), function ($query) use ($request) {
            $buscar = trim((string) $request->input('buscar'));
            $query->where(function ($subquery) use ($buscar) {
                $subquery->where('productos.nombre', 'like', '%' . $buscar . '%')
                    ->orWhere('productos.descripcion', 'like', '%' . $buscar . '%');
            });
        })
        ->orderBy('productos.nombre')
        ->get();

    return view('layouts.catalogo', compact('tiendas', 'categorias', 'productos'));
};

Route::get('/', $catalogo)->name('inicio');
Route::get('/catalogo', $catalogo)->name('catalogo');
Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('login.store');
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::resource('categoria', CategoriaController::class)->except(['show']);
    Route::resource('cliente', ClienteController::class)->except(['show']);
    Route::resource('facturas', FacturasController::class)->except(['show']);
    Route::resource('productos', ProductosController::class)->except(['show']);
    Route::resource('detalle_facturas', DetalleFacturaController::class)->except(['show']);
    Route::resource('tiendas', TiendaController::class);
});
