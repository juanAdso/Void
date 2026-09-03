<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
Use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
Route::resource('categoria', CategoriaController::class);
Route::resource('cliente', ClienteController::class);