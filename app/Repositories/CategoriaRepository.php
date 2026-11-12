<?php

namespace App\Repositories;

use App\Models\Categoria;
use App\Repositories\Interfaces\CategoriaRepositoryInterface;

class CategoriaRepository implements CategoriaRepositoryInterface
{
    public function listarTodo(?int $tiendaId)
    {
        $consulta = Categoria::query()
            ->withCount(['productos' => function ($query) use ($tiendaId) {
                if ($tiendaId !== null) {
                    $query->where('productos.id_tienda', $tiendaId);
                }
            }]);

        if ($tiendaId !== null) {
            $consulta->where('id_tienda', $tiendaId);
        }

        return $consulta->orderBy('nombre')->get();
    }

    public function guardar(array $datos, int $tiendaId)
    {
        $datos['id_tienda'] = $tiendaId;

        return Categoria::create($datos);
    }

    public function eliminar(int $id, ?int $tiendaId)
    {
        $consulta = Categoria::query();

        if ($tiendaId !== null) {
            $consulta->where('id_tienda', $tiendaId);
        }

        $categoria = $consulta->findOrFail($id);

        if ($categoria->productos()->exists()) {
            return false;
        }

        return $categoria->delete();
    }

    public function buscarPorId(int $id, ?int $tiendaId)
    {
        $consulta = Categoria::query();

        if ($tiendaId !== null) {
            $consulta->where('id_tienda', $tiendaId);
        }

        return $consulta->findOrFail($id);
    }

    public function actualizar(int $id, array $datos, ?int $tiendaId)
    {
        $consulta = Categoria::query();

        if ($tiendaId !== null) {
            $consulta->where('id_tienda', $tiendaId);
        }

        $categoria = $consulta->findOrFail($id);
        unset($datos['id_tienda']);
        $categoria->update($datos);

        return $categoria->refresh();
    }
}
