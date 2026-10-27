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
            $producto = Productos::query()->lockForUpdate()->findOrFail($datos['id_producto']);
            $cantidad = (int) $datos['cantidad'];

            if ($producto->stock < $cantidad) {
                throw ValidationException::withMessages([
                    'cantidad' => 'No hay suficiente stock para la cantidad solicitada.',
                ]);
            }

            $precio = (float) $datos['precio_unitario'];
            $detalle = DetalleFactura::create([
                'id_factura' => $datos['id_factura'],
                'id_producto' => $producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'subtotal' => round($cantidad * $precio, 2),
            ]);

            $producto->decrement('stock', $cantidad);
            $this->recalcularFactura((int) $detalle->id_factura);

            return $detalle;
        });
    }

    public function eliminar(int $id)
    {
        return DB::transaction(function () use ($id) {
            $detalle = DetalleFactura::query()->lockForUpdate()->findOrFail($id);
            $producto = Productos::query()->lockForUpdate()->findOrFail($detalle->id_producto);
            $facturaId = (int) $detalle->id_factura;

            $producto->increment('stock', (int) $detalle->cantidad);
            $detalle->delete();
            $this->recalcularFactura($facturaId);

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
            $facturaAnterior = (int) $detalle->id_factura;
            $productoAnterior = Productos::query()->lockForUpdate()->findOrFail($detalle->id_producto);
            $cantidadAnterior = (int) $detalle->cantidad;
            $productoNuevoId = (int) $datos['id_producto'];
            $cantidadNueva = (int) $datos['cantidad'];

            $productoAnterior->increment('stock', $cantidadAnterior);

            $productoNuevo = $productoNuevoId === (int) $productoAnterior->id
                ? $productoAnterior->refresh()
                : Productos::query()->lockForUpdate()->findOrFail($productoNuevoId);

            if ($productoNuevo->stock < $cantidadNueva) {
                throw ValidationException::withMessages([
                    'cantidad' => 'No hay suficiente stock para la cantidad solicitada.',
                ]);
            }

            $productoNuevo->decrement('stock', $cantidadNueva);
            $precio = (float) $datos['precio_unitario'];
            $detalle->update([
                'id_factura' => $datos['id_factura'],
                'id_producto' => $productoNuevoId,
                'cantidad' => $cantidadNueva,
                'precio_unitario' => $precio,
                'subtotal' => round($cantidadNueva * $precio, 2),
            ]);

            $this->recalcularFactura($facturaAnterior);
            if ((int) $datos['id_factura'] !== $facturaAnterior) {
                $this->recalcularFactura((int) $datos['id_factura']);
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
