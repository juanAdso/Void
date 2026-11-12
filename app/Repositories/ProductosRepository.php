<?php

namespace App\Repositories;

use App\Models\Categoria;
use App\Models\DetalleFactura;
use App\Models\Productos;
use App\Repositories\Interfaces\ProductoRepositoryInterface;

class ProductosRepository implements ProductoRepositoryInterface
{
    public function listarTodo(?int $tiendaId)
    {
        $consulta = Productos::query()->with('categoria');

        if ($tiendaId !== null) {
            $consulta->where('id_tienda', $tiendaId);
        }

        return $consulta->orderBy('id')->get();
    }

    public function guardar(array $datos, int $tiendaId)
    {
        Categoria::query()
            ->where('id_tienda', $tiendaId)
            ->findOrFail($datos['id_categoria']);

        $datos['id_tienda'] = $tiendaId;

        return Productos::create($datos);
    }

    public function eliminar(int $id, ?int $tiendaId)
    {
        $consulta = Productos::query();

        if ($tiendaId !== null) {
            $consulta->where('id_tienda', $tiendaId);
        }

        $producto = $consulta->findOrFail($id);

        if (DetalleFactura::where('id_producto', $producto->id)->exists()) {
            return false;
        }

        return $producto->delete();
    }

    public function buscarPorId(int $id, ?int $tiendaId)
    {
        $consulta = Productos::query()->with('categoria');

        if ($tiendaId !== null) {
            $consulta->where('id_tienda', $tiendaId);
        }

        return $consulta->findOrFail($id);
    }

    public function actualizar(int $id, array $datos, ?int $tiendaId)
    {
        $consulta = Productos::query();

        if ($tiendaId !== null) {
            $consulta->where('id_tienda', $tiendaId);
        }

        $producto = $consulta->findOrFail($id);
        $tiendaDestino = $tiendaId ?? (int) $producto->id_tienda;

        if (isset($datos['id_categoria'])) {
            Categoria::query()
                ->where('id_tienda', $tiendaDestino)
                ->findOrFail($datos['id_categoria']);
        }

        unset($datos['id_tienda']);
        $producto->update($datos);

        return $producto->refresh();
    }
}
