<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DetalleFacturaController;
use App\Http\Controllers\FacturasController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\TiendaController;
use App\Models\Categoria;
use App\Models\Productos;
use App\Models\Tienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
                $subquery->where('productos.nombre', 'like', '%'.$buscar.'%')
                    ->orWhere('productos.descripcion', 'like', '%'.$buscar.'%');
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
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('modulo:ver dashboard')
        ->name('dashboard.index');

    // Categorías
    Route::get('/categoria', [CategoriaController::class, 'index'])
        ->middleware('modulo:ver categorias')
        ->name('categoria.index');
    Route::get('/categoria/create', [CategoriaController::class, 'create'])
        ->middleware('modulo:crear categorias')
        ->name('categoria.create');
    Route::post('/categoria', [CategoriaController::class, 'store'])
        ->middleware('modulo:crear categorias')
        ->name('categoria.store');
    Route::get('/categoria/{categorium}/edit', [CategoriaController::class, 'edit'])
        ->middleware('modulo:editar categorias')
        ->name('categoria.edit');
    Route::match(['put', 'patch'], '/categoria/{categorium}', [CategoriaController::class, 'update'])
        ->middleware('modulo:editar categorias')
        ->name('categoria.update');
    Route::delete('/categoria/{categorium}', [CategoriaController::class, 'destroy'])
        ->middleware('modulo:eliminar categorias')
        ->name('categoria.destroy');

    // Clientes
    Route::get('/cliente', [ClienteController::class, 'index'])
        ->middleware('modulo:ver clientes')
        ->name('cliente.index');
    Route::get('/cliente/create', [ClienteController::class, 'create'])
        ->middleware('modulo:crear clientes')
        ->name('cliente.create');
    Route::post('/cliente', [ClienteController::class, 'store'])
        ->middleware('modulo:crear clientes')
        ->name('cliente.store');
    Route::get('/cliente/{cliente}/edit', [ClienteController::class, 'edit'])
        ->middleware('modulo:editar clientes')
        ->name('cliente.edit');
    Route::match(['put', 'patch'], '/cliente/{cliente}', [ClienteController::class, 'update'])
        ->middleware('modulo:editar clientes')
        ->name('cliente.update');
    Route::delete('/cliente/{cliente}', [ClienteController::class, 'destroy'])
        ->middleware('modulo:eliminar clientes')
        ->name('cliente.destroy');

    // Facturas
    Route::get('/facturas', [FacturasController::class, 'index'])
        ->middleware('modulo:ver facturas')
        ->name('facturas.index');
    Route::get('/facturas/create', [FacturasController::class, 'create'])
        ->middleware('modulo:crear facturas')
        ->name('facturas.create');
    Route::post('/facturas', [FacturasController::class, 'store'])
        ->middleware('modulo:crear facturas')
        ->name('facturas.store');
    Route::get('/facturas/{factura}/edit', [FacturasController::class, 'edit'])
        ->middleware('modulo:editar facturas')
        ->name('facturas.edit');
    Route::match(['put', 'patch'], '/facturas/{factura}', [FacturasController::class, 'update'])
        ->middleware('modulo:editar facturas')
        ->name('facturas.update');
    Route::delete('/facturas/{factura}', [FacturasController::class, 'destroy'])
        ->middleware('modulo:eliminar facturas')
        ->name('facturas.destroy');

    // Productos
    Route::get('/productos', [ProductosController::class, 'index'])
        ->middleware('modulo:ver productos')
        ->name('productos.index');
    Route::get('/productos/create', [ProductosController::class, 'create'])
        ->middleware('modulo:crear productos')
        ->name('productos.create');
    Route::post('/productos', [ProductosController::class, 'store'])
        ->middleware('modulo:crear productos')
        ->name('productos.store');
    Route::get('/productos/{producto}/edit', [ProductosController::class, 'edit'])
        ->middleware('modulo:editar productos')
        ->name('productos.edit');
    Route::match(['put', 'patch'], '/productos/{producto}', [ProductosController::class, 'update'])
        ->middleware('modulo:editar productos')
        ->name('productos.update');
    Route::delete('/productos/{producto}', [ProductosController::class, 'destroy'])
        ->middleware('modulo:eliminar productos')
        ->name('productos.destroy');

    // Detalle de Facturas
    Route::get('/detalle_facturas', [DetalleFacturaController::class, 'index'])
        ->middleware('modulo:ver detallefacturas')
        ->name('detalle_facturas.index');
    Route::get('/detalle_facturas/create', [DetalleFacturaController::class, 'create'])
        ->middleware('modulo:crear detallefacturas')
        ->name('detalle_facturas.create');
    Route::post('/detalle_facturas', [DetalleFacturaController::class, 'store'])
        ->middleware('modulo:crear detallefacturas')
        ->name('detalle_facturas.store');
    Route::get('/detalle_facturas/{detalle_factura}/edit', [DetalleFacturaController::class, 'edit'])
        ->middleware('modulo:editar detallefacturas')
        ->name('detalle_facturas.edit');
    Route::match(['put', 'patch'], '/detalle_facturas/{detalle_factura}', [DetalleFacturaController::class, 'update'])
        ->middleware('modulo:editar detallefacturas')
        ->name('detalle_facturas.update');
    Route::delete('/detalle_facturas/{detalle_factura}', [DetalleFacturaController::class, 'destroy'])
        ->middleware('modulo:eliminar detallefacturas')
        ->name('detalle_facturas.destroy');

    // Tiendas
    Route::get('/tiendas', [TiendaController::class, 'index'])
        ->middleware('modulo:ver tiendas')
        ->name('tiendas.index');
    Route::get('/tiendas/create', [TiendaController::class, 'create'])
        ->middleware('modulo:crear tiendas')
        ->name('tiendas.create');
    Route::post('/tiendas', [TiendaController::class, 'store'])
        ->middleware('modulo:crear tiendas')
        ->name('tiendas.store');
    Route::get('/tiendas/{tienda}', [TiendaController::class, 'show'])
        ->middleware('modulo:ver tiendas')
        ->name('tiendas.show');
    Route::get('/tiendas/{tienda}/edit', [TiendaController::class, 'edit'])
        ->middleware('modulo:editar tiendas')
        ->name('tiendas.edit');
    Route::match(['put', 'patch'], '/tiendas/{tienda}', [TiendaController::class, 'update'])
        ->middleware('modulo:editar tiendas')
        ->name('tiendas.update');
    Route::delete('/tiendas/{tienda}', [TiendaController::class, 'destroy'])
        ->middleware('modulo:eliminar tiendas')
        ->name('tiendas.destroy');
});
