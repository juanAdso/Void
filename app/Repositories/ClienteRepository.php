<?php

namespace App\Repositories;

use App\Models\Cliente;
use App\Repositories\Interfaces\ClienteRepositoryInterface;

class ClienteRepository implements ClienteRepositoryInterface
{
    public function listarTodo(?int $tiendaId)
    {
        $consulta = Cliente::query();

        if ($tiendaId !== null) {
            $consulta->where('id_tienda', $tiendaId);
        }

        return $consulta->orderBy('nombre')->orderBy('apellido')->get();
    }

    public function guardar(array $datos, int $tiendaId)
    {
        $datos['id_tienda'] = $tiendaId;

        return Cliente::create($datos);
    }

    public function eliminar(int $id, ?int $tiendaId)
    {
        $consulta = Cliente::query();

        if ($tiendaId !== null) {
            $consulta->where('id_tienda', $tiendaId);
        }

        $cliente = $consulta->findOrFail($id);

        if ($cliente->facturas()->exists()) {
            return false;
        }

        return $cliente->delete();
    }

    public function buscarPorId(int $id, ?int $tiendaId)
    {
        $consulta = Cliente::query();

        if ($tiendaId !== null) {
            $consulta->where('id_tienda', $tiendaId);
        }

        return $consulta->findOrFail($id);
    }

    public function actualizar(int $id, array $datos, ?int $tiendaId)
    {
        $consulta = Cliente::query();

        if ($tiendaId !== null) {
            $consulta->where('id_tienda', $tiendaId);
        }

        $cliente = $consulta->findOrFail($id);
        unset($datos['id_tienda']);
        $cliente->update($datos);

        return $cliente->refresh();
    }
}
