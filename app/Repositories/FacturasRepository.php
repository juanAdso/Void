<?php

namespace App\Repositories;

use App\Models\Facturas;
use App\Repositories\Interfaces\FacturasRepositoryInterface;

class FacturasRepository implements FacturasRepositoryInterface
{
    public function listarTodo()
    {
        $facturas = Facturas::with('cliente')->get();
        return $facturas;
    }

    public function guardar(array $datos)
    {
        Facturas::create($datos);
    }

    public function eliminar(int $id)
    {
        Facturas::destroy($id);
    }

    public function buscarPorId(int $id)
    {
        $facturas  = Facturas::findOrFail($id);
        return $facturas;
    }

    public function actualizar(int $id, array $datos)
    {
        $factura = Facturas::findOrFail($id);
        $factura->update($datos);
    }

}