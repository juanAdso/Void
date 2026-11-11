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
    public function listarTodo(?int $tiendaId = null)
    {
        $query = DetalleFactura::with(['factura.cliente', 'producto']);

        if ($tiendaId) {
            $query->whereHas('factura.usuario', fn ($q) => $q->where('id_tienda', $tiendaId));
        }

        return $query->orderBy('id')->get();
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

            if ($factura->estado === 'pagada') {
                throw ValidationException::withMessages([
                    'id_factura' => 'No se pueden agregar productos a una factura pagada.',
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
                'id_tienda' => $producto->id_tienda,
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

    public function eliminar(int $id, ?int $tiendaId = null)
    {
        return DB::transaction(function () use ($id, $tiendaId) {
            $detalleQuery = DetalleFactura::query()->lockForUpdate()->where('id', $id);
            if ($tiendaId) {
                $detalleQuery->whereHas('factura.usuario', fn ($q) => $q->where('id_tienda', $tiendaId));
            }
            $detalle = $detalleQuery->firstOrFail();

            $factura = Facturas::query()->lockForUpdate()->findOrFail($detalle->id_factura);

            if ($factura->estado === 'anulada') {
                throw ValidationException::withMessages([
                    'id_factura' => 'No se pueden eliminar detalles de una factura anulada.',
                ]);
            }

            if ($factura->estado === 'pagada') {
                throw ValidationException::withMessages([
                    'id_factura' => 'No se pueden eliminar detalles de una factura pagada. Debe gestionarse mediante devolución controlada.',
                ]);
            }

            $producto = Productos::query()->lockForUpdate()->findOrFail($detalle->id_producto);
            $cantidad = (int) $detalle->cantidad;

            $producto->increment('stock', $cantidad);

            MovimientoInventario::create([
                'id_producto' => $producto->id,
                'id_tienda' => $producto->id_tienda,
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

    public function buscarPorId(int $id, ?int $tiendaId = null)
    {
        $query = DetalleFactura::with(['factura', 'producto'])->where('id', $id);

        if ($tiendaId) {
            $query->whereHas('factura.usuario', fn ($q) => $q->where('id_tienda', $tiendaId));
        }

        return $query->firstOrFail();
    }

    public function actualizar(int $id, array $datos)
    {
        return DB::transaction(function () use ($id, $datos) {
            $detalle = DetalleFactura::query()->lockForUpdate()->findOrFail($id);
            $facturaAnteriorId = (int) $detalle->id_factura;
            $facturaNuevaId = (int) $datos['id_factura'];

            // Bloquear facturas involucradas en orden para evitar deadlocks
            $facturasIds = array_unique([$facturaAnteriorId, $facturaNuevaId]);
            sort($facturasIds);

            $facturasBloqueadas = Facturas::query()
                ->whereIn('id', $facturasIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $facturaAnterior = $facturasBloqueadas->get($facturaAnteriorId);
            $facturaNueva = $facturasBloqueadas->get($facturaNuevaId);

            if (! $facturaAnterior || ! $facturaNueva) {
                throw ValidationException::withMessages([
                    'id_factura' => 'Factura especificada no encontrada.',
                ]);
            }

            if ($facturaAnterior->estado === 'anulada' || $facturaNueva->estado === 'anulada') {
                throw ValidationException::withMessages([
                    'id_factura' => 'No se pueden modificar ni mover productos de o hacia una factura anulada.',
                ]);
            }

            if ($facturaAnterior->estado === 'pagada' || $facturaNueva->estado === 'pagada') {
                throw ValidationException::withMessages([
                    'id_factura' => 'No se pueden modificar detalles de una factura pagada.',
                ]);
            }

            $productoAnteriorId = (int) $detalle->id_producto;
            $productoNuevoId = (int) $datos['id_producto'];

            // Bloquear productos involucrados en orden ascendente para evitar deadlocks
            $productosIds = array_unique([$productoAnteriorId, $productoNuevoId]);
            sort($productosIds);

            $productosBloqueados = Productos::query()
                ->whereIn('id', $productosIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $productoAnterior = $productosBloqueados->get($productoAnteriorId);
            $productoNuevo = $productosBloqueados->get($productoNuevoId);

            if (! $productoAnterior || ! $productoNuevo) {
                throw ValidationException::withMessages([
                    'id_producto' => 'Producto especificado no encontrado.',
                ]);
            }

            $cantidadAnterior = (int) $detalle->cantidad;
            $cantidadNueva = (int) $datos['cantidad'];

            if ($productoAnteriorId === $productoNuevoId) {
                // Mismo producto, posible cambio de cantidad
                $diferencia = $cantidadNueva - $cantidadAnterior;
                if ($diferencia > 0) {
                    if ($productoAnterior->stock < $diferencia) {
                        throw ValidationException::withMessages([
                            'cantidad' => 'No hay suficiente stock para la cantidad solicitada.',
                        ]);
                    }
                    $productoAnterior->decrement('stock', $diferencia);
                    MovimientoInventario::create([
                        'id_producto' => $productoAnterior->id,
                        'id_tienda' => $productoAnterior->id_tienda,
                        'id_usuario' => auth()->id() ?? $facturaNueva->id_usuario,
                        'tipo_movimiento' => 'ajuste_edicion',
                        'cantidad' => -$diferencia,
                        'fecha' => now(),
                        'motivo' => "Aumento de {$diferencia} unidad(es) en detalle factura #{$facturaNueva->id}",
                    ]);
                } elseif ($diferencia < 0) {
                    $unidadesDevueltas = abs($diferencia);
                    $productoAnterior->increment('stock', $unidadesDevueltas);
                    MovimientoInventario::create([
                        'id_producto' => $productoAnterior->id,
                        'id_tienda' => $productoAnterior->id_tienda,
                        'id_usuario' => auth()->id() ?? $facturaNueva->id_usuario,
                        'tipo_movimiento' => 'ajuste_edicion',
                        'cantidad' => $unidadesDevueltas,
                        'fecha' => now(),
                        'motivo' => "Disminución de {$unidadesDevueltas} unidad(es) en detalle factura #{$facturaNueva->id}",
                    ]);
                }
            } else {
                // Cambio de producto: restituir stock a producto anterior y descontar de nuevo
                $productoAnterior->increment('stock', $cantidadAnterior);
                MovimientoInventario::create([
                    'id_producto' => $productoAnterior->id,
                    'id_tienda' => $productoAnterior->id_tienda,
                    'id_usuario' => auth()->id() ?? $facturaAnterior->id_usuario,
                    'tipo_movimiento' => 'cambio_producto_restitucion',
                    'cantidad' => $cantidadAnterior,
                    'fecha' => now(),
                    'motivo' => "Restitución por cambio de producto en factura #{$facturaAnterior->id}",
                ]);

                // Refrescar estado del producto nuevo
                $productoNuevo->refresh();

                if ($productoNuevo->stock < $cantidadNueva) {
                    throw ValidationException::withMessages([
                        'cantidad' => 'No hay suficiente stock para el nuevo producto seleccionado.',
                    ]);
                }

                $productoNuevo->decrement('stock', $cantidadNueva);
                MovimientoInventario::create([
                    'id_producto' => $productoNuevo->id,
                    'id_tienda' => $productoNuevo->id_tienda,
                    'id_usuario' => auth()->id() ?? $facturaNueva->id_usuario,
                    'tipo_movimiento' => 'cambio_producto_salida',
                    'cantidad' => -$cantidadNueva,
                    'fecha' => now(),
                    'motivo' => "Salida por cambio de producto en factura #{$facturaNueva->id}",
                ]);
            }

            $precio = (float) $datos['precio_unitario'];
            $subtotalDetalle = round($cantidadNueva * $precio, 2);

            $detalle->update([
                'id_factura' => $facturaNueva->id,
                'id_producto' => $productoNuevo->id,
                'cantidad' => $cantidadNueva,
                'precio_unitario' => $precio,
                'subtotal' => $subtotalDetalle,
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
        $subtotal = (float) $factura->detalle_factura()->sum('subtotal');
        $subtotalRedondeado = round($subtotal, 2);
        $iva = round($subtotalRedondeado * 0.19, 2);
        $total = round($subtotalRedondeado + $iva, 2);

        $factura->update([
            'subtotal' => $subtotalRedondeado,
            'iva' => $iva,
            'total' => $total,
        ]);
    }
}
