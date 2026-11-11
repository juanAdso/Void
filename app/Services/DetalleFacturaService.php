<?php

namespace App\Services;

use App\Repositories\Interfaces\DetalleFacturaRepositoryInterface;

class DetalleFacturaService
{
    public function __construct(private DetalleFacturaRepositoryInterface $detalle_factura_repository) {}

    public function listarTodo(?int $tiendaId = null)
    {
        return $this->detalle_factura_repository->listarTodo($tiendaId);
    }

    public function editar(int $id, ?int $tiendaId = null)
    {
        return $this->detalle_factura_repository->buscarPorId($id, $tiendaId);
    }

    public function guardar(array $datos)
    {
        return $this->detalle_factura_repository->guardar($datos);
    }

    public function eliminar(int $id, ?int $tiendaId = null)
    {
        return $this->detalle_factura_repository->eliminar($id, $tiendaId);
    }

    public function buscarPorId(int $id, ?int $tiendaId = null)
    {
        return $this->detalle_factura_repository->buscarPorId($id, $tiendaId);
    }

    public function actualizar(int $id, array $datos)
    {
        return $this->detalle_factura_repository->actualizar($id, $datos);
    }
}
