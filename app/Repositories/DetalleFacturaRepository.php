<?php

namespace App\Repositories;

use App\Models\DetalleFactura;
use App\Models\Facturas;
use App\Models\Productos;
use App\Repositories\Interfaces\DetalleFacturaRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DetalleFacturaRepository implements DetalleFacturaRepositoryInterface
{
    public function listarTodo()
    {
        return DetalleFactura::with(['factura.cliente', 'producto'])->orderBy('id')->get();
    }

    public function guardar(array $datos)
    {
        return DB::transaction(function () use ($datos) {
            $factura = Facturas::query()->lockForUpdate()->findOrFail($datos['id_factura']);

            if ($factura->estado === 'anulada') {
                throw ValidationException::withMessages([
                    'id_factura' => 'No se pueden agregar productos a una factura anulada.',
                ]);
            }

            $producto = Productos::query()->lockForUpdate()->findOrFail($datos['id_producto']);
            $cantidad = (int) $datos['cantidad'];

            if ($producto->stock < $cantidad) {
                throw ValidationException::withMessages([
                    'cantidad' => 'No hay suficiente stock para la cantidad solicitada.',
                ]);
            }

            $precio = (float) $datos['precio_unitario'];
            $detalle = DetalleFactura::create([
                'id_factura' => $factura->id,
                'id_producto' => $producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'subtotal' => round($cantidad * $precio, 2),
            ]);

            $producto->decrement('stock', $cantidad);
            $this->recalcularFactura((int) $factura->id);

            return $detalle;
        });
    }

    public function eliminar(int $id)
    {
        return DB::transaction(function () use ($id) {
            $detalle = DetalleFactura::query()->lockForUpdate()->findOrFail($id);
            $factura = Facturas::query()->lockForUpdate()->findOrFail($detalle->id_factura);
            $producto = Productos::query()->lockForUpdate()->findOrFail($detalle->id_producto);

            if ($factura->estado !== 'anulada') {
                $producto->increment('stock', (int) $detalle->cantidad);
            }

            $detalle->delete();
            $this->recalcularFactura((int) $factura->id);

            return true;
        });
    }

    public function buscarPorId(int $id)
    {
        return DetalleFactura::with(['factura', 'producto'])->findOrFail($id);
    }

    public function actualizar(int $id, array $datos)
    {
        return DB::transaction(function () use ($id, $datos) {
            $detalle = DetalleFactura::query()->lockForUpdate()->findOrFail($id);
            $facturaAnterior = Facturas::query()->lockForUpdate()->findOrFail($detalle->id_factura);
            $facturaNueva = (int) $datos['id_factura'] === (int) $facturaAnterior->id
                ? $facturaAnterior
                : Facturas::query()->lockForUpdate()->findOrFail($datos['id_factura']);

            if ($facturaNueva->estado === 'anulada') {
                throw ValidationException::withMessages([
                    'id_factura' => 'No se pueden mover productos a una factura anulada.',
                ]);
            }

            $productoAnterior = Productos::query()->lockForUpdate()->findOrFail($detalle->id_producto);
            $productoNuevoId = (int) $datos['id_producto'];
            $productoNuevo = $productoNuevoId === (int) $productoAnterior->id
                ? $productoAnterior
                : Productos::query()->lockForUpdate()->findOrFail($productoNuevoId);

            $cantidadAnterior = (int) $detalle->cantidad;
            $cantidadNueva = (int) $datos['cantidad'];

            if ($facturaAnterior->estado !== 'anulada') {
                $productoAnterior->increment('stock', $cantidadAnterior);
                if ($productoNuevoId === (int) $productoAnterior->id) {
                    $productoNuevo->refresh();
                }
            }

            if ($productoNuevo->stock < $cantidadNueva) {
                throw ValidationException::withMessages([
                    'cantidad' => 'No hay suficiente stock para la cantidad solicitada.',
                ]);
            }

            $productoNuevo->decrement('stock', $cantidadNueva);
            $precio = (float) $datos['precio_unitario'];

            $detalle->update([
                'id_factura' => $facturaNueva->id,
                'id_producto' => $productoNuevoId,
                'cantidad' => $cantidadNueva,
                'precio_unitario' => $precio,
                'subtotal' => round($cantidadNueva * $precio, 2),
            ]);

            $this->recalcularFactura((int) $facturaAnterior->id);
            if ((int) $facturaNueva->id !== (int) $facturaAnterior->id) {
                $this->recalcularFactura((int) $facturaNueva->id);
            }

            return $detalle->refresh();
        });
    }

    private function recalcularFactura(int $facturaId): void
    {
        $factura = Facturas::query()->lockForUpdate()->findOrFail($facturaId);
        $subtotal = (float) $factura->detalle_facturas()->sum('subtotal');
        $iva = round($subtotal * 0.19, 2);

        $factura->update([
            'subtotal' => round($subtotal, 2),
            'iva' => $iva,
            'total' => round($subtotal + $iva, 2),
        ]);
    }
}
