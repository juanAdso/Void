<?php

namespace App\Services;

use App\Repositories\Interfaces\DetalleFacturaRepositoryInterface;

class DetalleFacturaService
{
    public function __construct(private DetalleFacturaRepositoryInterface $detalle_factura_repository) {}

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
        return $this->detalle_factura_repository->guardar($datos);
    }

    public function eliminar(int $id)
    {
        return $this->detalle_factura_repository->eliminar($id);
    }

    public function buscarPorId(int $id)
    {
        return $this->detalle_factura_repository->buscarPorId($id);
    }

    public function actualizar(int $id, array $datos)
    {
        return $this->detalle_factura_repository->actualizar($id, $datos);
    }
}
