<?php

namespace App\Repositories;

use App\Models\Productos;

class ProductosRepository
{
    public function listarTodo()
    {
        $productos = Productos::with('categoria')->get();
        return $productos;
    }

    public function guardar(array $datos)
    {
        Productos::create($datos);
    }

    public function eliminar(int $id)
    {
        Productos::destroy($id);
    }

    public function buscarPorId(int $id)
    {
        $productos  = Productos::findOrFail($id);
        return $productos;
    }

    public function actualizar(int $id, array $datos)
    {
        $producto = Productos::findOrFail($id);
        $producto->update($datos);
    }
}
