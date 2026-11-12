<?php

namespace App\Services;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Facturas;
use App\Models\Productos;
use App\Models\Tienda;
use App\Models\User;
use App\Repositories\Interfaces\BusquedaRepositoryInterface;

class BusquedaService
{
    public function __construct(private BusquedaRepositoryInterface $repository) {}

    public function buscar(User $usuario, string $termino): array
    {
        $registros = $this->repository->buscar($usuario, $termino);
        $secciones = [];

        if ($registros['productos']->isNotEmpty()) {
            $secciones[] = [
                'titulo' => 'Productos',
                'items' => $registros['productos']->map(fn (Productos $producto) => [
                    'titulo' => $producto->nombre,
                    'detalle' => 'Stock: '.$producto->stock.' · Precio: $'.number_format((float) $producto->precio, 0, ',', '.'),
                    'url' => $usuario->can('editar productos')
                        ? route('productos.edit', $producto->id)
                        : route('productos.index'),
                ])->all(),
            ];
        }

        if ($registros['categorias']->isNotEmpty()) {
            $secciones[] = [
                'titulo' => 'Categorías',
                'items' => $registros['categorias']->map(fn (Categoria $categoria) => [
                    'titulo' => $categoria->nombre,
                    'detalle' => $categoria->descripcion ?: 'Categoría de productos',
                    'url' => $usuario->can('editar categorias')
                        ? route('categoria.edit', $categoria->id)
                        : route('categoria.index'),
                ])->all(),
            ];
        }

        if ($registros['clientes']->isNotEmpty()) {
            $secciones[] = [
                'titulo' => 'Clientes',
                'items' => $registros['clientes']->map(fn (Cliente $cliente) => [
                    'titulo' => trim($cliente->nombre.' '.$cliente->apellido),
                    'detalle' => implode(' · ', array_filter([
                        'Documento: '.$cliente->documento,
                        $cliente->correo,
                        $cliente->telefono,
                    ])),
                    'url' => $usuario->can('editar clientes')
                        ? route('cliente.edit', $cliente->id)
                        : route('cliente.index'),
                ])->all(),
            ];
        }

        if ($registros['facturas']->isNotEmpty()) {
            $secciones[] = [
                'titulo' => 'Facturas',
                'items' => $registros['facturas']->map(fn (Facturas $factura) => [
                    'titulo' => 'Factura #'.$factura->id,
                    'detalle' => implode(' · ', array_filter([
                        $factura->cliente
                            ? 'Cliente: '.trim($factura->cliente->nombre.' '.$factura->cliente->apellido)
                            : null,
                        'Fecha: '.$factura->fecha?->format('d/m/Y'),
                        'Estado: '.ucfirst((string) $factura->estado),
                        'Total: $'.number_format((float) $factura->total, 0, ',', '.'),
                    ])),
                    'url' => $usuario->can('editar facturas')
                        ? route('facturas.edit', $factura->id)
                        : route('facturas.index'),
                ])->all(),
            ];
        }

        if ($registros['empleados']->isNotEmpty()) {
            $secciones[] = [
                'titulo' => 'Empleados',
                'items' => $registros['empleados']->map(fn (User $empleado) => [
                    'titulo' => trim($empleado->nombre.' '.$empleado->apellido),
                    'detalle' => implode(' · ', array_filter([
                        'Documento: '.$empleado->documento,
                        $empleado->correo,
                        $empleado->estado ? 'Activo' : 'Inactivo',
                    ])),
                    'url' => route('empleados.edit', $empleado->id),
                ])->all(),
            ];
        }

        if ($registros['tiendas']->isNotEmpty()) {
            $secciones[] = [
                'titulo' => 'Tiendas',
                'items' => $registros['tiendas']->map(fn (Tienda $tienda) => [
                    'titulo' => $tienda->nombre,
                    'detalle' => implode(' · ', array_filter([
                        $tienda->direccion,
                        $tienda->telefono,
                        $tienda->correo,
                    ])),
                    'url' => $usuario->can('editar tiendas')
                        ? route('tiendas.edit', $tienda->id)
                        : route('tiendas.index'),
                ])->all(),
            ];
        }

        return $secciones;
    }
}
