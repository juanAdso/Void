<?php

namespace App\Repositories;

use App\Models\Tienda;
use App\Repositories\Interfaces\TiendaRepositoryInterface;

class TiendaRepository implements TiendaRepositoryInterface
{
    public function listarTodo()
    {
        return Tienda::withCount('productos')->orderBy('id', 'desc')->get();
    }

    public function guardar(array $datos)
    {
        return Tienda::create($datos);
    }

    public function eliminar(int $id)
    {
        $tienda = Tienda::findOrFail($id);

        if ($tienda->productos()->exists()) {
            return false;
        }

        return $tienda->delete();
    }

    public function buscarPorId(int $id)
    {
        return Tienda::with('productos')->findOrFail($id);
    }

    public function actualizar(int $id, array $datos)
    {
        $tienda = Tienda::findOrFail($id);
        $tienda->update($datos);

        return $tienda->refresh();
    }
}
