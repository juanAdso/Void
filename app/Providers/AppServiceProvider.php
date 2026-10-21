<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Interfaces\FacturasRepositoryInterface::class,
            \App\Repositories\FacturasRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\ClienteRepositoryInterface::class,
            \App\Repositories\ClienteRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\CategoriaRepositoryInterface::class,
            \App\Repositories\CategoriaRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\ProductoRepositoryInterface::class,
            \App\Repositories\ProductosRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\DetalleFacturaRepositoryInterface::class,
            \App\Repositories\DetalleFacturaRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\TiendaRepositoryInterface::class,
            \App\Repositories\TiendaRepository::class
        );
    }

    public function boot(): void
    {
        Paginator::useTailwind();
    }
}
