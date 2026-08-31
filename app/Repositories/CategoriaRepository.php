<?php

namespace App\Repositories;

use App\Models\Categoria;

class CategoriaRepository
{
    public function listarTodo()
    {
        $categorias = Categoria::all();
        return $categorias;
    }

    public function guardar (array $datos)
    {
        Categoria::create($datos);
    }  
    
    public function eliminar(int $id)
    {
        Categoria::destroy($id);
    }

    public function buscarPorId(int $id)
    {
        $categorias  = Categoria::findOrFail($id);
        return $categorias;
    }

    public function actualizar(int $id, array $datos)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->update($datos);
    }
}

    