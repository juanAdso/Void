<?php

namespace App\Repositories;

use App\Models\DetalleFactura;

class DetalleFacturaRepository
{
    public function listarTodo()
    {
        $detalleFacturas = DetalleFactura::with('factura')->get();
        return $detalleFacturas;
    }

    public function guardar(array $datos)
    {
        DetalleFactura::create($datos);
    }

    public function eliminar(int $id)
    {
        DetalleFactura::destroy($id);
    }

    public function buscarPorId(int $id)
    {
        $detalleFacturas = DetalleFactura::findOrFail($id);
        return $detalleFacturas;
    }

    public function actualizar(int $id, array $datos)
    {
        $detalleFactura = DetalleFactura::findOrFail($id);
        $detalleFactura->update($datos);
    }
}
