<?php

namespace App\Repositories;

use App\Models\Facturas;
use App\Models\MovimientoInventario;
use App\Models\Productos;
use App\Repositories\Interfaces\FacturasRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FacturasRepository implements FacturasRepositoryInterface
{
    public function listarTodo(?int $tiendaId = null)
    {
        $query = Facturas::with(['cliente', 'detalle_factura']);

        if ($tiendaId) {
            $query->whereHas('usuario', fn ($q) => $q->where('id_tienda', $tiendaId));
        }

        return $query->orderByDesc('fecha')->get();
    }

    public function guardar(array $datos)
    {
        $datos['id_usuario'] = auth()->id();
        $datos['subtotal'] = 0;
        $datos['iva'] = 0;
        $datos['total'] = 0;

        return Facturas::create($datos);
    }

    public function eliminar(int $id, ?int $tiendaId = null)
    {
        $query = Facturas::where('id', $id);

        if ($tiendaId) {
            $query->whereHas('usuario', fn ($q) => $q->where('id_tienda', $tiendaId));
        }

        $factura = $query->firstOrFail();

        // No permitir eliminar facturas con detalles
        if ($factura->detalle_factura()->exists()) {
            return false;
        }

        // Tampoco permitir eliminar facturas que ya fueron pagadas o anuladas
        if ($factura->estado !== 'pendiente') {
            return false;
        }

        return $factura->delete();
    }

    public function buscarPorId(int $id, ?int $tiendaId = null)
    {
        $query = Facturas::with(['cliente', 'detalle_factura'])->where('id', $id);

        if ($tiendaId) {
            $query->whereHas('usuario', fn ($q) => $q->where('id_tienda', $tiendaId));
        }

        return $query->firstOrFail();
    }

    public function actualizar(int $id, array $datos)
    {
        return DB::transaction(function () use ($id, $datos) {
            $factura = Facturas::query()->lockForUpdate()->findOrFail($id);
            $estadoAnterior = (string) $factura->estado;
            $estadoNuevo = isset($datos['estado']) ? (string) $datos['estado'] : $estadoAnterior;

            // Regla comercial: Una factura anulada es inmutable
            if ($estadoAnterior === 'anulada') {
                throw ValidationException::withMessages([
                    'estado' => 'Una factura anulada es inmutable y no se puede modificar ni reactivar.',
                ]);
            }

            // Regla comercial: Una factura pagada no permite cambiar datos comerciales libremente
            if ($estadoAnterior === 'pagada') {
                if ($estadoNuevo === 'pendiente') {
                    throw ValidationException::withMessages([
                        'estado' => 'Una factura pagada no puede regresar a estado pendiente.',
                    ]);
                }

                // Si se mantiene en pagada, verificar que no intenten alterar cliente ni fecha
                if ($estadoNuevo === 'pagada') {
                    if (isset($datos['id_cliente']) && (int) $datos['id_cliente'] !== (int) $factura->id_cliente) {
                        throw ValidationException::withMessages([
                            'id_cliente' => 'No se puede modificar el cliente de una factura pagada.',
                        ]);
                    }
                    if (isset($datos['fecha']) && $datos['fecha'] !== $factura->fecha?->format('Y-m-d') && $datos['fecha'] !== $factura->fecha?->toDateString()) {
                        throw ValidationException::withMessages([
                            'fecha' => 'No se puede modificar la fecha de una factura pagada.',
                        ]);
                    }
                }
            }

            // Nunca permitir sobreescribir totales desde el exterior
            unset($datos['subtotal'], $datos['iva'], $datos['total'], $datos['id_usuario']);

            // Si pasa a anulada (desde pendiente o pagada): devolución controlada de inventario
            if ($estadoAnterior !== 'anulada' && $estadoNuevo === 'anulada') {
                // Obtener detalles bloqueando los productos en orden ascendente por ID
                $detalles = $factura->detalle_factura()->get();
                $productosIds = $detalles->pluck('id_producto')->unique()->sort()->values();

                if ($productosIds->isNotEmpty()) {
                    $productosBloqueados = Productos::query()
                        ->whereIn('id', $productosIds)
                        ->lockForUpdate()
                        ->get()
                        ->keyBy('id');

                    foreach ($detalles as $detalle) {
                        $producto = $productosBloqueados->get($detalle->id_producto);
                        if ($producto) {
                            $cantidad = (int) $detalle->cantidad;
                            $producto->increment('stock', $cantidad);

                            MovimientoInventario::create([
                                'id_producto' => $producto->id,
                                'id_tienda' => $producto->id_tienda,
                                'id_usuario' => auth()->id() ?? $factura->id_usuario,
                                'tipo_movimiento' => 'anulacion_factura',
                                'cantidad' => $cantidad,
                                'fecha' => now(),
                                'motivo' => "Restitución por anulación de factura #{$factura->id} (Estado anterior: {$estadoAnterior})",
                            ]);
                        }
                    }
                }
            }

            $factura->update($datos);

            return $factura->refresh();
        });
    }
}
