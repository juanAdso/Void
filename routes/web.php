<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusquedaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DetalleFacturaController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\FacturasController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\PublicPortalController;
use App\Http\Controllers\RegistroTiendaController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\SuperadministradorController;
use App\Http\Controllers\TiendaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPortalController::class, 'index'])->name('inicio');
Route::get('/catalogo', [PublicPortalController::class, 'catalogo'])->name('catalogo');
Route::get('/tienda/{id}', [PublicPortalController::class, 'tienda'])->name('portal.tienda');
Route::get('/producto/{id}', [PublicPortalController::class, 'producto'])->name('portal.producto');

Route::get('/registro-tienda', [RegistroTiendaController::class, 'create'])->name('registro.tienda');
Route::post('/registro-tienda', [RegistroTiendaController::class, 'store'])->name('registro.tienda.store');

Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('login.store');
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::prefix('superadministrador')->name('superadmin.')->middleware(['auth', 'superadministrador'])->group(function () {
    Route::get('/', [SuperadministradorController::class, 'dashboard'])->name('dashboard');
    Route::get('/empresas', [SuperadministradorController::class, 'empresas'])->name('empresas.index');
    Route::get('/empresas/{tienda}', [SuperadministradorController::class, 'showEmpresa'])->name('empresas.show');
    Route::patch('/empresas/{tienda}/estado', [SuperadministradorController::class, 'cambiarEstadoEmpresa'])->name('empresas.estado');
    Route::post('/empresas/{tienda}/administradores', [SuperadministradorController::class, 'storeAdministrador'])->name('empresas.administradores.store');
    Route::patch('/empresas/{tienda}/usuarios/{usuario}/estado', [SuperadministradorController::class, 'cambiarEstadoUsuario'])->name('empresas.usuarios.estado');
});

Route::get('/password/reset', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [PasswordResetController::class, 'sendResetCode'])->name('password.email');
Route::get('/password/code', [PasswordResetController::class, 'showResetForm'])->name('password.code');
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.update');

Route::middleware('auth')->group(function () {

    Route::get('/buscar', [BusquedaController::class, 'index'])
        ->middleware('modulo')
        ->name('busqueda.global');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('modulo:ver dashboard')
        ->name('dashboard.index');

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

    Route::get('/facturas', [FacturasController::class, 'index'])
        ->middleware('modulo:ver facturas')
        ->name('facturas.index');
    Route::get('/facturas/create', [FacturasController::class, 'create'])
        ->middleware('modulo:crear facturas')
        ->name('facturas.create');
    Route::post('/facturas', [FacturasController::class, 'store'])
        ->middleware('modulo:crear facturas')
        ->name('facturas.store');
    Route::get('/facturas/{factura}', [FacturasController::class, 'show'])
        ->middleware('modulo:ver facturas')
        ->name('facturas.show');
    Route::get('/facturas/{factura}/edit', [FacturasController::class, 'edit'])
        ->middleware('modulo:editar facturas')
        ->name('facturas.edit');
    Route::match(['put', 'patch'], '/facturas/{factura}', [FacturasController::class, 'update'])
        ->middleware('modulo:editar facturas')
        ->name('facturas.update');
    Route::delete('/facturas/{factura}', [FacturasController::class, 'destroy'])
        ->middleware('modulo:eliminar facturas')
        ->name('facturas.destroy');

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

    Route::post('/detalle_facturas', [DetalleFacturaController::class, 'store'])
        ->middleware('modulo:crear detallefacturas')
        ->name('detalle_facturas.store');
    Route::match(['put', 'patch'], '/detalle_facturas/{detalle_factura}', [DetalleFacturaController::class, 'update'])
        ->middleware('modulo:editar detallefacturas')
        ->name('detalle_facturas.update');
    Route::delete('/detalle_facturas/{detalle_factura}', [DetalleFacturaController::class, 'destroy'])
        ->middleware('modulo:eliminar detallefacturas')
        ->name('detalle_facturas.destroy');

    Route::get('/reportes', [ReportesController::class, 'index'])
        ->middleware('modulo:gestionar seguridad')
        ->name('reportes.index');

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

    Route::get('/empleados', [EmpleadoController::class, 'index'])
        ->middleware('modulo:gestionar seguridad')
        ->name('empleados.index');
    Route::get('/empleados/create', [EmpleadoController::class, 'create'])
        ->middleware('modulo:gestionar seguridad')
        ->name('empleados.create');
    Route::post('/empleados', [EmpleadoController::class, 'store'])
        ->middleware('modulo:gestionar seguridad')
        ->name('empleados.store');
    Route::get('/empleados/{empleado}/edit', [EmpleadoController::class, 'edit'])
        ->middleware('modulo:gestionar seguridad')
        ->name('empleados.edit');
    Route::match(['put', 'patch'], '/empleados/{empleado}', [EmpleadoController::class, 'update'])
        ->middleware('modulo:gestionar seguridad')
        ->name('empleados.update');
    Route::delete('/empleados/{empleado}', [EmpleadoController::class, 'destroy'])
        ->middleware('modulo:gestionar seguridad')
        ->name('empleados.destroy');
});
