<?php

namespace App\Repositories;

use App\Models\Facturas;
use App\Repositories\Interfaces\FacturasRepositoryInterface;

class FacturasRepository implements FacturasRepositoryInterface
{
    public function listarTodo()
    {
        return Facturas::with(['cliente', 'detalle_facturas'])->orderByDesc('fecha')->get();
    }

    public function guardar(array $datos)
    {
        return Facturas::create($datos);
    }

    public function eliminar(int $id)
    {
        $factura = Facturas::findOrFail($id);

        if ($factura->detalle_facturas()->exists()) {
            return false;
        }

        return $factura->delete();
    }

    public function buscarPorId(int $id)
    {
        return Facturas::with(['cliente', 'detalle_facturas'])->findOrFail($id);
    }

    public function actualizar(int $id, array $datos)
    {
        $factura = Facturas::findOrFail($id);
        $factura->update($datos);

        return $factura->refresh();
    }
}
