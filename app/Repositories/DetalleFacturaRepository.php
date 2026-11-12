<?php

namespace App\Repositories;

use App\Models\DetalleFactura;
use App\Models\Facturas;
use App\Models\MovimientoInventario;
use App\Models\Productos;
use App\Repositories\Interfaces\DetalleFacturaRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DetalleFacturaRepository implements DetalleFacturaRepositoryInterface
{
    public function listarTodo(int $tiendaId)
    {
        return DetalleFactura::query()
            ->with(['factura.cliente', 'producto'])
            ->whereHas('factura.usuario', fn ($query) => $query->where('id_tienda', $tiendaId))
            ->orderBy('id')
            ->get();
    }

    public function guardar(array $datos, int $tiendaId)
    {
        return DB::transaction(function () use ($datos, $tiendaId) {
            $factura = Facturas::query()
                ->whereHas('usuario', fn ($query) => $query->where('id_tienda', $tiendaId))
                ->lockForUpdate()
                ->findOrFail($datos['id_factura']);

            if ($factura->estado !== 'pendiente') {
                throw ValidationException::withMessages([
                    'id_factura' => 'Solo se pueden agregar productos a facturas pendientes.',
                ]);
            }

            $producto = Productos::query()
                ->where('id_tienda', $tiendaId)
                ->lockForUpdate()
                ->findOrFail($datos['id_producto']);

            $cantidad = (int) $datos['cantidad'];

            if ($producto->stock < $cantidad) {
                throw ValidationException::withMessages([
                    'cantidad' => 'No hay suficiente stock para la cantidad solicitada.',
                ]);
            }

            $precio = (float) $datos['precio_unitario'];
            $subtotalDetalle = round($cantidad * $precio, 2);

            $detalle = DetalleFactura::create([
                'id_factura' => $factura->id,
                'id_producto' => $producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'subtotal' => $subtotalDetalle,
            ]);

            $producto->decrement('stock', $cantidad);

            MovimientoInventario::create([
                'id_producto' => $producto->id,
                'id_tienda' => $tiendaId,
                'id_usuario' => auth()->id() ?? $factura->id_usuario,
                'tipo_movimiento' => 'venta_detalle',
                'cantidad' => -$cantidad,
                'fecha' => now(),
                'motivo' => "Incorporación de {$cantidad} unidad(es) a la factura #{$factura->id}",
            ]);

            $this->recalcularFactura((int) $factura->id);

            return $detalle;
        });
    }

    public function eliminar(int $id, int $tiendaId)
    {
        return DB::transaction(function () use ($id, $tiendaId) {
            $detalle = DetalleFactura::query()
                ->whereHas('factura.usuario', fn ($query) => $query->where('id_tienda', $tiendaId))
                ->lockForUpdate()
                ->findOrFail($id);

            $factura = Facturas::query()
                ->whereHas('usuario', fn ($query) => $query->where('id_tienda', $tiendaId))
                ->lockForUpdate()
                ->findOrFail($detalle->id_factura);

            if ($factura->estado !== 'pendiente') {
                throw ValidationException::withMessages([
                    'id_factura' => 'Solo se pueden retirar detalles de facturas pendientes.',
                ]);
            }

            $producto = Productos::query()
                ->where('id_tienda', $tiendaId)
                ->lockForUpdate()
                ->findOrFail($detalle->id_producto);

            $cantidad = (int) $detalle->cantidad;
            $producto->increment('stock', $cantidad);

            MovimientoInventario::create([
                'id_producto' => $producto->id,
                'id_tienda' => $tiendaId,
                'id_usuario' => auth()->id() ?? $factura->id_usuario,
                'tipo_movimiento' => 'devolucion_detalle',
                'cantidad' => $cantidad,
                'fecha' => now(),
                'motivo' => "Eliminación de detalle de factura #{$factura->id}",
            ]);

            $detalle->delete();
            $this->recalcularFactura((int) $factura->id);

            return true;
        });
    }

    public function buscarPorId(int $id, int $tiendaId)
    {
        return DetalleFactura::query()
            ->with(['factura', 'producto'])
            ->whereHas('factura.usuario', fn ($query) => $query->where('id_tienda', $tiendaId))
            ->findOrFail($id);
    }

    public function actualizar(int $id, array $datos, int $tiendaId)
    {
        return DB::transaction(function () use ($id, $datos, $tiendaId) {
            $detalle = DetalleFactura::query()
                ->whereHas('factura.usuario', fn ($query) => $query->where('id_tienda', $tiendaId))
                ->lockForUpdate()
                ->findOrFail($id);

            $facturaAnteriorId = (int) $detalle->id_factura;
            $facturaNuevaId = (int) $datos['id_factura'];

            $facturasIds = array_unique([$facturaAnteriorId, $facturaNuevaId]);
            sort($facturasIds);

            $facturasBloqueadas = Facturas::query()
                ->whereHas('usuario', fn ($query) => $query->where('id_tienda', $tiendaId))
                ->whereIn('id', $facturasIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $facturaAnterior = $facturasBloqueadas->get($facturaAnteriorId);
            $facturaNueva = $facturasBloqueadas->get($facturaNuevaId);

            if (! $facturaAnterior || ! $facturaNueva) {
                throw ValidationException::withMessages([
                    'id_factura' => 'Las facturas deben pertenecer a tu tienda.',
                ]);
            }

            if ($facturaAnterior->estado !== 'pendiente' || $facturaNueva->estado !== 'pendiente') {
                throw ValidationException::withMessages([
                    'id_factura' => 'Solo se pueden modificar detalles entre facturas pendientes.',
                ]);
            }

            $productoAnteriorId = (int) $detalle->id_producto;
            $productoNuevoId = (int) $datos['id_producto'];

            $productosIds = array_unique([$productoAnteriorId, $productoNuevoId]);
            sort($productosIds);

            $productosBloqueados = Productos::query()
                ->where('id_tienda', $tiendaId)
                ->whereIn('id', $productosIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $productoAnterior = $productosBloqueados->get($productoAnteriorId);
            $productoNuevo = $productosBloqueados->get($productoNuevoId);

            if (! $productoAnterior || ! $productoNuevo) {
                throw ValidationException::withMessages([
                    'id_producto' => 'Los productos deben pertenecer a tu tienda.',
                ]);
            }

            $cantidadAnterior = (int) $detalle->cantidad;
            $cantidadNueva = (int) $datos['cantidad'];

            if ($productoAnteriorId === $productoNuevoId) {
                $diferencia = $cantidadNueva - $cantidadAnterior;

                if ($diferencia > 0) {
                    if ($productoAnterior->stock < $diferencia) {
                        throw ValidationException::withMessages([
                            'cantidad' => 'No hay suficiente stock para la cantidad solicitada.',
                        ]);
                    }

                    $productoAnterior->decrement('stock', $diferencia);
                    $cantidadMovimiento = -$diferencia;
                } elseif ($diferencia < 0) {
                    $unidadesDevueltas = abs($diferencia);
                    $productoAnterior->increment('stock', $unidadesDevueltas);
                    $cantidadMovimiento = $unidadesDevueltas;
                } else {
                    $cantidadMovimiento = 0;
                }

                if ($cantidadMovimiento !== 0) {
                    MovimientoInventario::create([
                        'id_producto' => $productoAnterior->id,
                        'id_tienda' => $tiendaId,
                        'id_usuario' => auth()->id() ?? $facturaNueva->id_usuario,
                        'tipo_movimiento' => 'ajuste_edicion',
                        'cantidad' => $cantidadMovimiento,
                        'fecha' => now(),
                        'motivo' => "Ajuste de cantidad en detalle factura #{$facturaNueva->id}",
                    ]);
                }
            } else {
                $productoAnterior->increment('stock', $cantidadAnterior);

                MovimientoInventario::create([
                    'id_producto' => $productoAnterior->id,
                    'id_tienda' => $tiendaId,
                    'id_usuario' => auth()->id() ?? $facturaAnterior->id_usuario,
                    'tipo_movimiento' => 'cambio_producto_restitucion',
                    'cantidad' => $cantidadAnterior,
                    'fecha' => now(),
                    'motivo' => "Restitución por cambio de producto en factura #{$facturaAnterior->id}",
                ]);

                if ($productoNuevo->stock < $cantidadNueva) {
                    throw ValidationException::withMessages([
                        'cantidad' => 'No hay suficiente stock para el nuevo producto seleccionado.',
                    ]);
                }

                $productoNuevo->decrement('stock', $cantidadNueva);

                MovimientoInventario::create([
                    'id_producto' => $productoNuevo->id,
                    'id_tienda' => $tiendaId,
                    'id_usuario' => auth()->id() ?? $facturaNueva->id_usuario,
                    'tipo_movimiento' => 'cambio_producto_salida',
                    'cantidad' => -$cantidadNueva,
                    'fecha' => now(),
                    'motivo' => "Salida por cambio de producto en factura #{$facturaNueva->id}",
                ]);
            }

            $precio = (float) $datos['precio_unitario'];
            $detalle->update([
                'id_factura' => $facturaNueva->id,
                'id_producto' => $productoNuevo->id,
                'cantidad' => $cantidadNueva,
                'precio_unitario' => $precio,
                'subtotal' => round($cantidadNueva * $precio, 2),
            ]);

            $this->recalcularFactura((int) $facturaAnterior->id);

            if ($facturaNueva->id !== $facturaAnterior->id) {
                $this->recalcularFactura((int) $facturaNueva->id);
            }

            return $detalle->refresh();
        });
    }

    private function recalcularFactura(int $facturaId): void
    {
        $factura = Facturas::query()->lockForUpdate()->findOrFail($facturaId);
        $subtotal = round((float) $factura->detalle_factura()->sum('subtotal'), 2);
        $iva = round($subtotal * 0.19, 2);

        $factura->update([
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => round($subtotal + $iva, 2),
        ]);
    }
}
