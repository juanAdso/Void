<?php

namespace App\Repositories;

use App\Models\DetalleFactura;
use App\Models\Productos;
use App\Repositories\Interfaces\ProductoRepositoryInterface;

class ProductosRepository implements ProductoRepositoryInterface
{
    public function listarTodo(?int $tiendaId = null)
    {
        return Productos::with('categoria')
            ->when($tiendaId, fn ($q) => $q->where('id_tienda', $tiendaId))
            ->orderBy('id')
            ->get();
    }

    public function guardar(array $datos)
    {
        return Productos::create($datos);
    }

    public function eliminar(int $id, ?int $tiendaId = null)
    {
        $query = Productos::where('id', $id);
        if ($tiendaId) {
            $query->where('id_tienda', $tiendaId);
        }
        $producto = $query->firstOrFail();

        if (DetalleFactura::where('id_producto', $id)->exists()) {
            return false;
        }

        return $producto->delete();
    }

    public function buscarPorId(int $id, ?int $tiendaId = null)
    {
        $query = Productos::with('categoria')->where('id', $id);
        if ($tiendaId) {
            $query->where('id_tienda', $tiendaId);
        }

        return $query->firstOrFail();
    }

    public function actualizar(int $id, array $datos)
    {
        $producto = Productos::findOrFail($id);
        $producto->update($datos);

        return $producto->refresh();
    }
}
