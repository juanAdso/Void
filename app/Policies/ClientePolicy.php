<?php

namespace App\Policies;

use App\Models\Cliente;
use App\Models\User;

class ClientePolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user->id_tienda && $user->can('ver clientes');
    }

    public function view(User $user, Cliente $cliente): bool
    {
        return (bool) $user->id_tienda
            && (int) $cliente->id_tienda === (int) $user->id_tienda
            && $user->can('ver clientes');
    }

    public function create(User $user): bool
    {
        return (bool) $user->id_tienda && $user->can('crear clientes');
    }

    public function update(User $user, Cliente $cliente): bool
    {
        return (bool) $user->id_tienda
            && (int) $cliente->id_tienda === (int) $user->id_tienda
            && $user->can('editar clientes');
    }

    public function delete(User $user, Cliente $cliente): bool
    {
        return (bool) $user->id_tienda
            && (int) $cliente->id_tienda === (int) $user->id_tienda
            && $user->can('eliminar clientes');
    }

    public function restore(User $user, Cliente $cliente): bool
    {
        return $this->update($user, $cliente);
    }

    public function forceDelete(User $user, Cliente $cliente): bool
    {
        return $this->delete($user, $cliente);
    }
}
