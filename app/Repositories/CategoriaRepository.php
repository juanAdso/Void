<?php

namespace App\Repositories;

use App\Models\Categoria;
use App\Repositories\Interfaces\CategoriaRepositoryInterface;

class CategoriaRepository implements CategoriaRepositoryInterface
{
    public function listarTodo(int $tiendaId)
    {
        return Categoria::query()
            ->where('id_tienda', $tiendaId)
            ->withCount(['productos' => fn ($query) => $query->where('productos.id_tienda', $tiendaId)])
            ->orderBy('nombre')
            ->get();
    }

    public function guardar(array $datos, int $tiendaId)
    {
        $datos['id_tienda'] = $tiendaId;

        return Categoria::create($datos);
    }

    public function eliminar(int $id, int $tiendaId)
    {
        $categoria = Categoria::query()
            ->where('id_tienda', $tiendaId)
            ->findOrFail($id);

        if ($categoria->productos()->exists()) {
            return false;
        }

        return $categoria->delete();
    }

    public function buscarPorId(int $id, int $tiendaId)
    {
        return Categoria::query()
            ->where('id_tienda', $tiendaId)
            ->findOrFail($id);
    }

    public function actualizar(int $id, array $datos, int $tiendaId)
    {
        $categoria = Categoria::query()
            ->where('id_tienda', $tiendaId)
            ->findOrFail($id);

        unset($datos['id_tienda']);
        $categoria->update($datos);

        return $categoria->refresh();
    }
}
