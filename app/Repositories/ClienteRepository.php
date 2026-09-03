<?php

namespace App\Repositories;

use App\Models\Cliente;

class ClienteRepository
{
    public function listarTodo()
    {
        $clientes = Cliente::all();
        return $clientes;
    }

    public function guardar (array $datos)
    {
        Cliente::create($datos);
    }  
    
    public function eliminar(int $id)
    {
        Cliente::destroy($id);
    }

    public function buscarPorId(int $id)
    {
        $clientes  = Cliente::findOrFail($id);
        return $clientes;
    }

    public function actualizar(int $id, array $datos)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->update($datos);
    }
}