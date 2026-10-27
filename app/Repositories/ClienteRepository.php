<?php

namespace App\Repositories;

use App\Models\Cliente;
use App\Repositories\Interfaces\ClienteRepositoryInterface;

class ClienteRepository implements ClienteRepositoryInterface
{
    public function listarTodo()
    {
        return Cliente::orderBy('id')->get();
    }

    public function guardar(array $datos)
    {
        return Cliente::create($datos);
    }

    public function eliminar(int $id)
    {
        $cliente = Cliente::findOrFail($id);

        if ($cliente->facturas()->exists()) {
            return false;
        }

        return $cliente->delete();
    }

    public function buscarPorId(int $id)
    {
        return Cliente::findOrFail($id);
    }

    public function actualizar(int $id, array $datos)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->update($datos);

        return $cliente->refresh();
    }
}
