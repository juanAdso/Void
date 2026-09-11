<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
Use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductosController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
Route::resource('categoria', CategoriaController::class);
Route::resource('cliente', ClienteController::class);
Route::resource('productos', ProductosController::class);