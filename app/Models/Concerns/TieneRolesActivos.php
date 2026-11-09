<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Collection;

trait TieneRolesActivos
{
    /**
     * Determina si el usuario está activo.
     */
    public function estaActivo(): bool
    {
        return (bool) $this->estado;
    }

    /**
     * Obtiene los roles del usuario solo si el usuario está activo.
     */
    public function getRolesActivos(): Collection
    {
        if (! $this->estaActivo()) {
            return new Collection;
        }

        return $this->roles;
    }

    /**
     * Comprueba si el usuario tiene un rol activo.
     */
    public function tieneRolActivo(string|array $roles): bool
    {
        if (! $this->estaActivo()) {
            return false;
        }

        return $this->hasRole($roles);
    }

    /**
     * Comprueba si el usuario tiene un permiso activo.
     */
    public function tienePermisoActivo(string $permiso): bool
    {
        if (! $this->estaActivo()) {
            return false;
        }

        return $this->hasPermissionTo($permiso);
    }

    /**
     * Comprueba si el usuario tiene alguno de los permisos indicados y está activo.
     */
    public function tieneAlgunPermisoActivo(array|string ...$permisos): bool
    {
        if (! $this->estaActivo()) {
            return false;
        }

        return $this->hasAnyPermission(...$permisos);
    }
}
