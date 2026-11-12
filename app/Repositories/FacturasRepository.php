<?php

namespace App\Repositories;

use App\Models\Cliente;
use App\Models\Facturas;
use App\Models\MovimientoInventario;
use App\Models\Productos;
use App\Repositories\Interfaces\FacturasRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FacturasRepository implements FacturasRepositoryInterface
{
    public function listarTodo(int $tiendaId)
    {
        return Facturas::query()
            ->with([
                'cliente' => fn ($query) => $query->where('id_tienda', $tiendaId),
                'detalle_factura' => fn ($query) => $query->whereHas(
                    'producto',
                    fn ($producto) => $producto->where('id_tienda', $tiendaId)
                ),
            ])
            ->whereHas('usuario', fn ($query) => $query->where('id_tienda', $tiendaId))
            ->orderByDesc('fecha')
            ->get();
    }

    public function guardar(array $datos, int $tiendaId)
    {
        $cliente = Cliente::query()
            ->where('id_tienda', $tiendaId)
            ->findOrFail($datos['id_cliente']);

        $datos['id_cliente'] = $cliente->id;
        $datos['id_usuario'] = auth()->id();
        $datos['subtotal'] = 0;
        $datos['iva'] = 0;
        $datos['total'] = 0;
        $datos['estado'] = 'pendiente';

        return Facturas::create($datos);
    }

    public function eliminar(int $id, int $tiendaId)
    {
        $factura = Facturas::query()
            ->whereHas('usuario', fn ($query) => $query->where('id_tienda', $tiendaId))
            ->findOrFail($id);

        if ($factura->detalle_factura()->exists() || $factura->estado !== 'pendiente') {
            return false;
        }

        return $factura->delete();
    }

    public function buscarPorId(int $id, int $tiendaId)
    {
        return Facturas::query()
            ->with([
                'cliente' => fn ($query) => $query->where('id_tienda', $tiendaId),
                'detalle_factura' => fn ($query) => $query->whereHas(
                    'producto',
                    fn ($producto) => $producto->where('id_tienda', $tiendaId)
                ),
            ])
            ->whereHas('usuario', fn ($query) => $query->where('id_tienda', $tiendaId))
            ->findOrFail($id);
    }

    public function actualizar(int $id, array $datos, int $tiendaId)
    {
        return DB::transaction(function () use ($id, $datos, $tiendaId) {
            $factura = Facturas::query()
                ->whereHas('usuario', fn ($query) => $query->where('id_tienda', $tiendaId))
                ->lockForUpdate()
                ->findOrFail($id);

            if (isset($datos['id_cliente'])) {
                Cliente::query()->where('id_tienda', $tiendaId)->findOrFail($datos['id_cliente']);
            }

            $estadoAnterior = (string) $factura->estado;
            $estadoNuevo = isset($datos['estado']) ? (string) $datos['estado'] : $estadoAnterior;

            if ($estadoAnterior === 'anulada') {
                throw ValidationException::withMessages([
                    'estado' => 'Una factura anulada es inmutable y no se puede modificar ni reactivar.',
                ]);
            }

            if ($estadoAnterior === 'pagada') {
                if ($estadoNuevo === 'pendiente') {
                    throw ValidationException::withMessages([
                        'estado' => 'Una factura pagada no puede regresar a estado pendiente.',
                    ]);
                }

                if ($estadoNuevo === 'pagada') {
                    if (isset($datos['id_cliente']) && (int) $datos['id_cliente'] !== (int) $factura->id_cliente) {
                        throw ValidationException::withMessages([
                            'id_cliente' => 'No se puede modificar el cliente de una factura pagada.',
                        ]);
                    }

                    if (isset($datos['fecha']) && $datos['fecha'] !== $factura->fecha?->toDateString()) {
                        throw ValidationException::withMessages([
                            'fecha' => 'No se puede modificar la fecha de una factura pagada.',
                        ]);
                    }
                }
            }

            unset($datos['subtotal'], $datos['iva'], $datos['total'], $datos['id_usuario'], $datos['id_tienda']);

            if ($estadoAnterior !== 'anulada' && $estadoNuevo === 'anulada') {
                $detalles = $factura->detalle_factura()->get();
                $productosIds = $detalles->pluck('id_producto')->unique()->sort()->values();

                if ($productosIds->isNotEmpty()) {
                    $productosBloqueados = Productos::query()
                        ->where('id_tienda', $tiendaId)
                        ->whereIn('id', $productosIds)
                        ->lockForUpdate()
                        ->get()
                        ->keyBy('id');

                    foreach ($detalles as $detalle) {
                        $producto = $productosBloqueados->get($detalle->id_producto);

                        if (! $producto) {
                            throw ValidationException::withMessages([
                                'estado' => 'La factura contiene un producto que ya no pertenece a esta tienda.',
                            ]);
                        }

                        $cantidad = (int) $detalle->cantidad;
                        $producto->increment('stock', $cantidad);

                        MovimientoInventario::create([
                            'id_producto' => $producto->id,
                            'id_tienda' => $tiendaId,
                            'id_usuario' => auth()->id() ?? $factura->id_usuario,
                            'tipo_movimiento' => 'anulacion_factura',
                            'cantidad' => $cantidad,
                            'fecha' => now(),
                            'motivo' => "Restitución por anulación de factura #{$factura->id} (Estado anterior: {$estadoAnterior})",
                        ]);
                    }
                }
            }

            $factura->update($datos);

            return $factura->refresh();
        });
    }
}
