<?php

namespace App\Providers;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Productos;
use App\Policies\CategoriaPolicy;
use App\Policies\ClientePolicy;
use App\Policies\ProductosPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class SeguridadServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Productos::class, ProductosPolicy::class);
        Gate::policy(Cliente::class, ClientePolicy::class);
        Gate::policy(Categoria::class, CategoriaPolicy::class);

        // El estado inactivo bloquea cualquier habilidad; no existe bypass automático
        // de permisos para administradores.
        Gate::before(function ($user) {
            if (method_exists($user, 'estaActivo') && ! $user->estaActivo()) {
                return false;
            }

            return null;
        });
    }
}
