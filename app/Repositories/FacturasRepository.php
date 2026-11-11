<?php

namespace App\Repositories;

use App\Models\Facturas;
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

        return Facturas::create($datos);
    }

    public function eliminar(int $id, ?int $tiendaId = null)
    {
        $query = Facturas::where('id', $id);

        if ($tiendaId) {
            $query->whereHas('usuario', fn ($q) => $q->where('id_tienda', $tiendaId));
        }

        $factura = $query->firstOrFail();

        if ($factura->detalle_factura()->exists()) {
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
            $estadoNuevo = (string) $datos['estado'];
            $detalles = $factura->detalle_factura()->get();

            if ($estadoAnterior !== 'anulada' && $estadoNuevo === 'anulada') {
                foreach ($detalles as $detalle) {
                    $producto = Productos::query()->lockForUpdate()->findOrFail($detalle->id_producto);
                    $producto->increment('stock', (int) $detalle->cantidad);
                }
            } elseif ($estadoAnterior === 'anulada' && $estadoNuevo !== 'anulada') {
                foreach ($detalles as $detalle) {
                    $producto = Productos::query()->lockForUpdate()->findOrFail($detalle->id_producto);

                    if ($producto->stock < (int) $detalle->cantidad) {
                        throw ValidationException::withMessages([
                            'estado' => 'No se puede reactivar esta factura porque no hay stock suficiente para todos sus productos.',
                        ]);
                    }

                    $producto->decrement('stock', (int) $detalle->cantidad);
                }
            }

            $factura->update($datos);

            return $factura->refresh();
        });
    }
}
