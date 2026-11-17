<?php

namespace App\Repositories;

use App\Models\Categoria;
use App\Models\DetalleFactura;
use App\Models\MovimientoInventario;
use App\Models\Productos;
use App\Repositories\Interfaces\ProductoRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProductosRepository implements ProductoRepositoryInterface
{
    public function listarTodo(int $tiendaId)
    {
        return Productos::query()
            ->with('categoria')
            ->where('id_tienda', $tiendaId)
            ->orderBy('id')
            ->get();
    }

    public function guardar(array $datos, int $tiendaId)
    {
        Categoria::query()
            ->where('id_tienda', $tiendaId)
            ->findOrFail($datos['id_categoria']);

        $datos['id_tienda'] = $tiendaId;

        return Productos::create($datos);
    }

    public function eliminar(int $id, int $tiendaId)
    {
        return DB::transaction(function () use ($id, $tiendaId) {
            $producto = Productos::query()
                ->where('id_tienda', $tiendaId)
                ->lockForUpdate()
                ->findOrFail($id);

            $tieneDetalles = DetalleFactura::query()
                ->where('id_producto', $producto->id)
                ->exists();

            $tieneMovimientos = MovimientoInventario::query()
                ->where('id_producto', $producto->id)
                ->exists();

            if ($tieneDetalles || $tieneMovimientos) {
                return false;
            }

            return $producto->delete();
        });
    }

    public function buscarPorId(int $id, int $tiendaId)
    {
        return Productos::query()
            ->with('categoria')
            ->where('id_tienda', $tiendaId)
            ->findOrFail($id);
    }

    public function actualizar(int $id, array $datos, int $tiendaId)
    {
        return DB::transaction(function () use ($id, $datos, $tiendaId) {
            if (isset($datos['id_categoria'])) {
                Categoria::query()
                    ->where('id_tienda', $tiendaId)
                    ->findOrFail($datos['id_categoria']);
            }

            $producto = Productos::query()
                ->where('id_tienda', $tiendaId)
                ->lockForUpdate()
                ->findOrFail($id);

            unset($datos['id_tienda']);

            if (array_key_exists('stock', $datos)) {
                $stockNuevo = (int) $datos['stock'];
                $diferencia = $stockNuevo - (int) $producto->stock;

                if ($diferencia !== 0) {
                    MovimientoInventario::create([
                        'id_producto' => $producto->id,
                        'id_tienda' => $tiendaId,
                        'id_usuario' => auth()->id(),
                        'tipo_movimiento' => 'ajuste_manual',
                        'cantidad' => $diferencia,
                        'fecha' => now(),
                        'motivo' => 'Ajuste manual de inventario del producto #'.$producto->id,
                    ]);
                }
            }

            $producto->update($datos);

            return $producto->refresh();
        });
    }
}
