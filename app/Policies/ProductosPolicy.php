<?php

namespace App\Policies;

use App\Models\Productos;
use App\Models\User;

class ProductosPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user->id_tienda && $user->can('ver productos');
    }

    public function view(User $user, Productos $producto): bool
    {
        return (bool) $user->id_tienda
            && (int) $producto->id_tienda === (int) $user->id_tienda
            && $user->can('ver productos');
    }

    public function create(User $user): bool
    {
        return (bool) $user->id_tienda && $user->can('crear productos');
    }

    public function update(User $user, Productos $producto): bool
    {
        return (bool) $user->id_tienda
            && (int) $producto->id_tienda === (int) $user->id_tienda
            && $user->can('editar productos');
    }

    public function delete(User $user, Productos $producto): bool
    {
        return (bool) $user->id_tienda
            && (int) $producto->id_tienda === (int) $user->id_tienda
            && $user->can('eliminar productos');
    }

    public function restore(User $user, Productos $producto): bool
    {
        return $this->update($user, $producto);
    }

    public function forceDelete(User $user, Productos $producto): bool
    {
        return $this->delete($user, $producto);
    }
}
