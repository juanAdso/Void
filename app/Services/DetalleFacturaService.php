<?php

namespace App\Services;

use App\Repositories\Interfaces\DetalleFacturaRepositoryInterface;

class DetalleFacturaService
{
    private DetalleFacturaRepositoryInterface $detalle_factura_repository;

    public function __construct(DetalleFacturaRepositoryInterface $detalle_factura_repository)
    {
        $this->detalle_factura_repository = $detalle_factura_repository;
    }

    public function listarTodo()
    {
        return $this->detalle_factura_repository->listarTodo();
    }

    public function editar(int $id)
    {
        return $this->detalle_factura_repository->buscarPorId($id);
    }

    public function guardar(array $datos)
    {
        $this->detalle_factura_repository->guardar($datos);
    }

    public function eliminar(int $id)
    {
        $this->detalle_factura_repository->eliminar($id);
    }

    public function buscarPorId(int $id)
    {
        $detalleFactura = $this->detalle_factura_repository->buscarPorId($id);
        return $detalleFactura;
    }

    public function actualizar(int $id, array $datos)
    {
        $this->detalle_factura_repository->actualizar($id, $datos);
    }
}
