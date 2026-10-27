<?php

namespace App\Repositories;

use App\Models\Categoria;
use App\Repositories\Interfaces\CategoriaRepositoryInterface;

class CategoriaRepository implements CategoriaRepositoryInterface
{
    public function listarTodo()
    {
        return Categoria::withCount('productos')->orderBy('id')->get();
    }

    public function guardar(array $datos)
    {
        return Categoria::create($datos);
    }

    public function eliminar(int $id)
    {
        $categoria = Categoria::findOrFail($id);

        if ($categoria->productos()->exists()) {
            return false;
        }

        return $categoria->delete();
    }

    public function buscarPorId(int $id)
    {
        return Categoria::findOrFail($id);
    }

    public function actualizar(int $id, array $datos)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->update($datos);

        return $categoria->refresh();
    }
}
