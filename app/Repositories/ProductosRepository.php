<?php

namespace App\Repositories;

use App\Models\Productos;
use App\Models\DetalleFactura;
use App\Repositories\Interfaces\ProductoRepositoryInterface;

class ProductosRepository implements ProductoRepositoryInterface
{
    public function listarTodo()
    {
        return Productos::with(['categoria', 'tienda'])->orderBy('id')->get();
    }

    public function guardar(array $datos)
    {
        return Productos::create($datos);
    }

    public function eliminar(int $id)
    {
        $producto = Productos::findOrFail($id);

        if (DetalleFactura::where('id_producto', $id)->exists()) {
            return false;
        }

        return $producto->delete();
    }

    public function buscarPorId(int $id)
    {
        return Productos::with(['categoria', 'tienda'])->findOrFail($id);
    }

    public function actualizar(int $id, array $datos)
    {
        $producto = Productos::findOrFail($id);
        $producto->update($datos);

        return $producto->refresh();
    }
}