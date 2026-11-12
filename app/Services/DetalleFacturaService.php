<?php

namespace App\Services;

use App\Repositories\Interfaces\DetalleFacturaRepositoryInterface;

class DetalleFacturaService
{
    public function __construct(private DetalleFacturaRepositoryInterface $detalle_factura_repository) {}

    public function listarTodo(?int $tiendaId)
    {
        return $this->detalle_factura_repository->listarTodo($tiendaId);
    }

    public function editar(int $id, ?int $tiendaId)
    {
        return $this->detalle_factura_repository->buscarPorId($id, $tiendaId);
    }

    public function guardar(array $datos, int $tiendaId)
    {
        return $this->detalle_factura_repository->guardar($datos, $tiendaId);
    }

    public function eliminar(int $id, ?int $tiendaId)
    {
        return $this->detalle_factura_repository->eliminar($id, $tiendaId);
    }

    public function buscarPorId(int $id, ?int $tiendaId)
    {
        return $this->detalle_factura_repository->buscarPorId($id, $tiendaId);
    }

    public function actualizar(int $id, array $datos, ?int $tiendaId)
    {
        return $this->detalle_factura_repository->actualizar($id, $datos, $tiendaId);
    }
}
