<?php

namespace App\Repositories;

use App\Models\Cliente;
use App\Models\DetalleFactura;
use App\Models\Facturas;
use App\Models\MovimientoInventario;
use App\Models\Productos;
use App\Repositories\Interfaces\FacturasRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Gestiona facturas y mantiene sincronizados sus efectos sobre el inventario.
 */
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

    /**
     * Elimina una factura pendiente, devuelve existencias y registra los movimientos en una transacción.
     */
    public function eliminar(int $id, int $tiendaId)
    {
        return DB::transaction(function () use ($id, $tiendaId) {
            $factura = Facturas::query()
                ->whereHas('usuario', fn ($query) => $query->where('id_tienda', $tiendaId))
                ->lockForUpdate()
                ->findOrFail($id);

            if ($factura->estado !== 'pendiente') {
                return false;
            }

            $detalles = DetalleFactura::query()
                ->where('id_factura', $factura->id)
                ->orderBy('id')
                ->get();

            $productosIds = $detalles->pluck('id_producto')->unique()->sort()->values();
            $productos = collect();

            if ($productosIds->isNotEmpty()) {
                $productos = Productos::query()
                    ->where('id_tienda', $tiendaId)
                    ->whereIn('id', $productosIds)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');
            }

            foreach ($detalles as $detalle) {
                $producto = $productos->get((int) $detalle->id_producto);

                if (! $producto) {
                    throw ValidationException::withMessages([
                        'factura' => 'No se puede eliminar la factura porque uno de sus productos no pertenece a esta tienda o ya no está disponible.',
                    ]);
                }

                $cantidad = (int) $detalle->cantidad;
                $producto->increment('stock', $cantidad);

                MovimientoInventario::create([
                    'id_producto' => $producto->id,
                    'id_tienda' => $tiendaId,
                    'id_usuario' => auth()->id() ?? $factura->id_usuario,
                    'tipo_movimiento' => 'eliminacion_factura',
                    'cantidad' => $cantidad,
                    'fecha' => now(),
                    'motivo' => "Devolución de {$cantidad} unidad(es) por eliminación de factura #{$factura->id}",
                ]);

                $detalle->delete();
            }

            return $factura->delete();
        });
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

    /**
     * Guarda en una sola transacción los cambios generales, los detalles y sus efectos en inventario.
     */
    public function actualizarCompleta(int $id, array $datos, int $tiendaId)
    {
        return DB::transaction(function () use ($id, $datos, $tiendaId) {
            $factura = Facturas::query()
                ->whereHas('usuario', fn ($query) => $query->where('id_tienda', $tiendaId))
                ->lockForUpdate()
                ->findOrFail($id);

            if ($factura->estado === 'anulada') {
                throw ValidationException::withMessages([
                    'estado' => 'Una factura anulada no se puede modificar.',
                ]);
            }

            if (isset($datos['id_cliente'])) {
                Cliente::query()
                    ->where('id_tienda', $tiendaId)
                    ->findOrFail($datos['id_cliente']);
            }

            $estadoAnterior = (string) $factura->estado;
            $estadoNuevo = isset($datos['estado']) ? (string) $datos['estado'] : $estadoAnterior;

            if ($estadoAnterior === 'pagada' && $estadoNuevo === 'pendiente') {
                throw ValidationException::withMessages([
                    'estado' => 'Una factura pagada no puede regresar a estado pendiente.',
                ]);
            }

            $detallesActuales = DetalleFactura::query()
                ->where('id_factura', $factura->id)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $detallesEntrada = $datos['detalles'] ?? [];
            $idsActualizados = [];
            $idsEliminados = array_values(array_unique(array_map('intval', $datos['eliminar_detalles'] ?? [])));
            $productosIds = $detallesActuales->pluck('id_producto')->map(fn ($valor) => (int) $valor)->all();

            foreach ($detallesEntrada as $detalleEntrada) {
                if (! empty($detalleEntrada['id'])) {
                    $detalleId = (int) $detalleEntrada['id'];

                    if (! $detallesActuales->has($detalleId) || in_array($detalleId, $idsActualizados, true)) {
                        throw ValidationException::withMessages([
                            'detalles' => 'Uno de los detalles no pertenece a esta factura o está repetido.',
                        ]);
                    }

                    $idsActualizados[] = $detalleId;
                }

                $productosIds[] = (int) $detalleEntrada['id_producto'];
            }

            foreach ($idsEliminados as $detalleId) {
                if (! $detallesActuales->has($detalleId) || in_array($detalleId, $idsActualizados, true)) {
                    throw ValidationException::withMessages([
                        'eliminar_detalles' => 'Uno de los detalles que intentas retirar no pertenece a esta factura.',
                    ]);
                }
            }

            $productosIds = array_values(array_unique($productosIds));
            sort($productosIds);

            $productos = Productos::query()
                ->where('id_tienda', $tiendaId)
                ->whereIn('id', $productosIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($productos->count() !== count($productosIds)) {
                throw ValidationException::withMessages([
                    'detalles' => 'Uno de los productos no pertenece a tu tienda o ya no está disponible.',
                ]);
            }

            $diferenciasStock = [];
            $movimientos = [];
            $actualizaciones = [];
            $nuevosDetalles = [];

            foreach ($detallesEntrada as $detalleEntrada) {
                $detalleId = ! empty($detalleEntrada['id']) ? (int) $detalleEntrada['id'] : null;
                $productoNuevoId = (int) $detalleEntrada['id_producto'];
                $productoNuevo = $productos->get($productoNuevoId);
                $cantidadNueva = (int) $detalleEntrada['cantidad'];
                $precioNuevo = round((float) $detalleEntrada['precio_unitario'], 2);

                if ($detalleId !== null) {
                    $detalleActual = $detallesActuales->get($detalleId);
                    $productoAnteriorId = (int) $detalleActual->id_producto;
                    $cantidadAnterior = (int) $detalleActual->cantidad;
                    $precioAnterior = round((float) $detalleActual->precio_unitario, 2);

                    if ($productoNuevoId !== $productoAnteriorId && ! $productoNuevo->estado) {
                        throw ValidationException::withMessages([
                            'detalles' => 'No se puede cambiar un detalle a un producto inactivo.',
                        ]);
                    }

                    if ($productoNuevoId === $productoAnteriorId) {
                        $diferencia = $cantidadAnterior - $cantidadNueva;
                        $diferenciasStock[$productoAnteriorId] = ($diferenciasStock[$productoAnteriorId] ?? 0) + $diferencia;

                        if ($diferencia !== 0 || $precioAnterior !== $precioNuevo) {
                            $tipo = $diferencia === 0 ? 'ajuste_valor_factura' : 'ajuste_edicion';
                            $movimientos[] = [
                                'id_producto' => $productoAnteriorId,
                                'tipo_movimiento' => $tipo,
                                'cantidad' => $diferencia,
                                'motivo' => "Edición factura #{$factura->id}: cantidad {$cantidadAnterior} a {$cantidadNueva}; valor unitario $".number_format($precioAnterior, 2, ',', '.').' a $'.number_format($precioNuevo, 2, ',', '.'),
                            ];
                        }
                    } else {
                        $diferenciasStock[$productoAnteriorId] = ($diferenciasStock[$productoAnteriorId] ?? 0) + $cantidadAnterior;
                        $diferenciasStock[$productoNuevoId] = ($diferenciasStock[$productoNuevoId] ?? 0) - $cantidadNueva;

                        $movimientos[] = [
                            'id_producto' => $productoAnteriorId,
                            'tipo_movimiento' => 'cambio_producto_restitucion',
                            'cantidad' => $cantidadAnterior,
                            'motivo' => "Factura #{$factura->id}: restitución por cambio de producto; valor anterior $".number_format($precioAnterior, 2, ',', '.'),
                        ];
                        $movimientos[] = [
                            'id_producto' => $productoNuevoId,
                            'tipo_movimiento' => 'cambio_producto_salida',
                            'cantidad' => -$cantidadNueva,
                            'motivo' => "Factura #{$factura->id}: nuevo producto, {$cantidadNueva} unidad(es); valor unitario $".number_format($precioNuevo, 2, ',', '.'),
                        ];
                    }

                    $actualizaciones[] = [
                        'detalle' => $detalleActual,
                        'id_producto' => $productoNuevoId,
                        'cantidad' => $cantidadNueva,
                        'precio_unitario' => $precioNuevo,
                    ];
                } else {
                    if (! $productoNuevo->estado) {
                        throw ValidationException::withMessages([
                            'detalles' => 'No se pueden agregar productos inactivos a una factura.',
                        ]);
                    }

                    $diferenciasStock[$productoNuevoId] = ($diferenciasStock[$productoNuevoId] ?? 0) - $cantidadNueva;
                    $movimientos[] = [
                        'id_producto' => $productoNuevoId,
                        'tipo_movimiento' => 'venta_detalle',
                        'cantidad' => -$cantidadNueva,
                        'motivo' => "Nuevo detalle en factura #{$factura->id}: {$cantidadNueva} unidad(es); valor unitario $".number_format($precioNuevo, 2, ',', '.').'; subtotal $'.number_format($cantidadNueva * $precioNuevo, 2, ',', '.'),
                    ];
                    $nuevosDetalles[] = [
                        'id_producto' => $productoNuevoId,
                        'cantidad' => $cantidadNueva,
                        'precio_unitario' => $precioNuevo,
                    ];
                }
            }

            foreach ($idsEliminados as $detalleId) {
                $detalle = $detallesActuales->get($detalleId);
                $productoId = (int) $detalle->id_producto;
                $cantidad = (int) $detalle->cantidad;
                $precio = round((float) $detalle->precio_unitario, 2);

                $diferenciasStock[$productoId] = ($diferenciasStock[$productoId] ?? 0) + $cantidad;
                $movimientos[] = [
                    'id_producto' => $productoId,
                    'tipo_movimiento' => 'devolucion_detalle',
                    'cantidad' => $cantidad,
                    'motivo' => "Retiro de detalle factura #{$factura->id}: {$cantidad} unidad(es); valor unitario $".number_format($precio, 2, ',', '.'),
                ];
            }

            foreach ($actualizaciones as $actualizacion) {
                $actualizacion['detalle']->update([
                    'id_producto' => $actualizacion['id_producto'],
                    'cantidad' => $actualizacion['cantidad'],
                    'precio_unitario' => $actualizacion['precio_unitario'],
                    'subtotal' => round($actualizacion['cantidad'] * $actualizacion['precio_unitario'], 2),
                ]);
            }

            foreach ($idsEliminados as $detalleId) {
                $detallesActuales->get($detalleId)->delete();
            }

            foreach ($nuevosDetalles as $detalleNuevo) {
                DetalleFactura::create([
                    'id_factura' => $factura->id,
                    'id_producto' => $detalleNuevo['id_producto'],
                    'cantidad' => $detalleNuevo['cantidad'],
                    'precio_unitario' => $detalleNuevo['precio_unitario'],
                    'subtotal' => round($detalleNuevo['cantidad'] * $detalleNuevo['precio_unitario'], 2),
                ]);
            }

            if ($estadoNuevo === 'anulada') {
                $detallesFinales = DetalleFactura::query()
                    ->where('id_factura', $factura->id)
                    ->orderBy('id')
                    ->get();

                foreach ($detallesFinales as $detalleFinal) {
                    $productoId = (int) $detalleFinal->id_producto;
                    $cantidad = (int) $detalleFinal->cantidad;
                    $precio = round((float) $detalleFinal->precio_unitario, 2);

                    $diferenciasStock[$productoId] = ($diferenciasStock[$productoId] ?? 0) + $cantidad;
                    $movimientos[] = [
                        'id_producto' => $productoId,
                        'tipo_movimiento' => 'anulacion_factura',
                        'cantidad' => $cantidad,
                        'motivo' => "Restitución por anulación de factura #{$factura->id}; {$cantidad} unidad(es); valor unitario $".number_format($precio, 2, ',', '.'),
                    ];
                }
            }

            foreach ($diferenciasStock as $productoId => $diferencia) {
                $producto = $productos->get((int) $productoId);
                $nuevoStock = (int) $producto->stock + (int) $diferencia;

                if ($nuevoStock < 0) {
                    throw ValidationException::withMessages([
                        'detalles' => 'No hay existencias suficientes de '.$producto->nombre.' para guardar los cambios solicitados.',
                    ]);
                }

                $producto->update(['stock' => $nuevoStock]);
            }

            foreach ($movimientos as $movimiento) {
                MovimientoInventario::create([
                    'id_producto' => $movimiento['id_producto'],
                    'id_tienda' => $tiendaId,
                    'id_usuario' => auth()->id() ?? $factura->id_usuario,
                    'tipo_movimiento' => $movimiento['tipo_movimiento'],
                    'cantidad' => $movimiento['cantidad'],
                    'fecha' => now(),
                    'motivo' => $movimiento['motivo'],
                ]);
            }

            $datosFactura = array_intersect_key($datos, array_flip(['id_cliente', 'fecha', 'estado']));
            $subtotal = round((float) DetalleFactura::query()->where('id_factura', $factura->id)->sum('subtotal'), 2);
            $iva = round($subtotal * 0.19, 2);

            $factura->update(array_merge($datosFactura, [
                'subtotal' => $subtotal,
                'iva' => $iva,
                'total' => round($subtotal + $iva, 2),
            ]));

            return $this->buscarPorId((int) $factura->id, $tiendaId);
        });
    }

    /**
     * Actualiza la factura y restituye el inventario cuando cambia al estado anulada.
     */
    public function actualizar(int $id, array $datos, int $tiendaId)
    {
        return $this->actualizarCompleta($id, $datos, $tiendaId);
    }
}
