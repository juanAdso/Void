<?php

namespace App\Repositories;

use App\Models\Tienda;
use App\Repositories\Interfaces\TiendaRepositoryInterface;

class TiendaRepository implements TiendaRepositoryInterface
{
    public function listarTodo(?int $tiendaId)
    {
        $consulta = Tienda::query()->withCount('productos');

        if ($tiendaId !== null) {
            $consulta->whereKey($tiendaId);
        }

        return $consulta->orderBy('nombre')->get();
    }

    public function eliminar(int $id, ?int $tiendaId)
    {
        $consulta = Tienda::query();

        if ($tiendaId !== null) {
            $consulta->whereKey($tiendaId);
        }

        $tienda = $consulta->findOrFail($id);

        if (
            $tienda->productos()->exists()
            || $tienda->usuarios()->exists()
            || $tienda->clientes()->exists()
            || $tienda->categorias()->exists()
            || $tienda->movimientosInventario()->exists()
        ) {
            return false;
        }

        return $tienda->delete();
    }

    public function buscarPorId(int $id, ?int $tiendaId)
    {
        $consulta = Tienda::query()->with('productos');

        if ($tiendaId !== null) {
            $consulta->whereKey($tiendaId);
        }

        return $consulta->whereKey($id)->firstOrFail();
    }

    public function actualizar(int $id, array $datos, ?int $tiendaId)
    {
        $consulta = Tienda::query();

        if ($tiendaId !== null) {
            $consulta->whereKey($tiendaId);
        }

        $tienda = $consulta->whereKey($id)->firstOrFail();

        unset($datos['id_tienda']);
        $tienda->update($datos);

        return $tienda->refresh();
    }
}
