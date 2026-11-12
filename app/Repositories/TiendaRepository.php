<?php

namespace App\Repositories;

use App\Models\Tienda;
use App\Repositories\Interfaces\TiendaRepositoryInterface;

class TiendaRepository implements TiendaRepositoryInterface
{
    public function listarTodo(int $tiendaId)
    {
        return Tienda::query()
            ->withCount('productos')
            ->whereKey($tiendaId)
            ->get();
    }

    public function eliminar(int $id, int $tiendaId)
    {
        $tienda = Tienda::query()->findOrFail($id);

        if ((int) $tienda->id !== $tiendaId) {
            abort(404);
        }

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

    public function buscarPorId(int $id, int $tiendaId)
    {
        return Tienda::query()
            ->with('productos')
            ->whereKey($tiendaId)
            ->whereKey($id)
            ->firstOrFail();
    }

    public function actualizar(int $id, array $datos, int $tiendaId)
    {
        $tienda = Tienda::query()
            ->whereKey($tiendaId)
            ->whereKey($id)
            ->firstOrFail();

        unset($datos['id_tienda']);
        $tienda->update($datos);

        return $tienda->refresh();
    }
}
