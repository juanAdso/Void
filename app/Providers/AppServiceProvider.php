<?php

namespace App\Providers;

use App\Repositories\BusquedaRepository;
use App\Repositories\CategoriaRepository;
use App\Repositories\ClienteRepository;
use App\Repositories\DetalleFacturaRepository;
use App\Repositories\FacturasRepository;
use App\Repositories\Interfaces\BusquedaRepositoryInterface;
use App\Repositories\Interfaces\CategoriaRepositoryInterface;
use App\Repositories\Interfaces\ClienteRepositoryInterface;
use App\Repositories\Interfaces\DetalleFacturaRepositoryInterface;
use App\Repositories\Interfaces\FacturasRepositoryInterface;
use App\Repositories\Interfaces\ProductoRepositoryInterface;
use App\Repositories\Interfaces\TiendaRepositoryInterface;
use App\Repositories\ProductosRepository;
use App\Repositories\TiendaRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            FacturasRepositoryInterface::class,
            FacturasRepository::class
        );
        $this->app->bind(
            ClienteRepositoryInterface::class,
            ClienteRepository::class
        );
        $this->app->bind(
            CategoriaRepositoryInterface::class,
            CategoriaRepository::class
        );
        $this->app->bind(
            ProductoRepositoryInterface::class,
            ProductosRepository::class
        );
        $this->app->bind(
            DetalleFacturaRepositoryInterface::class,
            DetalleFacturaRepository::class
        );
        $this->app->bind(
            TiendaRepositoryInterface::class,
            TiendaRepository::class
        );
        $this->app->bind(
            BusquedaRepositoryInterface::class,
            BusquedaRepository::class
        );
    }

    public function boot(): void
    {
        Paginator::useTailwind();
    }
}
