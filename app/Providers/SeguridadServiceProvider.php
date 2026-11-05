<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class SeguridadServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Concede implícitamente todos los permisos al rol 'administrador' a nivel de Gate
        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'estaActivo') && ! $user->estaActivo()) {
                return false;
            }

            if (method_exists($user, 'hasRole') && $user->hasRole('administrador')) {
                return true;
            }

            return null;
        });
    }
}
