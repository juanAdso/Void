<?php

namespace App\Repositories;

use App\Models\Cliente;
use App\Repositories\Interfaces\ClienteRepositoryInterface;

class ClienteRepository implements ClienteRepositoryInterface
{
    public function listarTodo(int $tiendaId)
    {
        return Cliente::query()
            ->where('id_tienda', $tiendaId)
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();
    }

    public function guardar(array $datos, int $tiendaId)
    {
        $datos['id_tienda'] = $tiendaId;

        return Cliente::create($datos);
    }

    public function eliminar(int $id, int $tiendaId)
    {
        $cliente = Cliente::query()
            ->where('id_tienda', $tiendaId)
            ->findOrFail($id);

        if ($cliente->facturas()->exists()) {
            return false;
        }

        return $cliente->delete();
    }

    public function buscarPorId(int $id, int $tiendaId)
    {
        return Cliente::query()
            ->where('id_tienda', $tiendaId)
            ->findOrFail($id);
    }

    public function actualizar(int $id, array $datos, int $tiendaId)
    {
        $cliente = Cliente::query()
            ->where('id_tienda', $tiendaId)
            ->findOrFail($id);

        unset($datos['id_tienda']);
        $cliente->update($datos);

        return $cliente->refresh();
    }
}
