<?php

namespace App\Services;

use App\Models\DetalleFactura;
use App\Models\Facturas;
use App\Models\MovimientoInventario;
use App\Models\Productos;

class RelacionesBloqueoService
{
    private const LIMITE_ITEMS = 8;

    public function paraCategoria(int $id, int $tiendaId): array
    {
        $q = Productos::query()->where('id_tienda', $tiendaId)->where('id_categoria', $id)->orderBy('nombre');
        $total = (clone $q)->count();
        if (! $total) {
            return [];
        }
        $items = (clone $q)->limit(self::LIMITE_ITEMS)->get(['id', 'nombre', 'stock', 'estado'])
            ->map(fn ($p) => ['id' => $p->id, 'titulo' => $p->nombre, 'detalle' => 'Producto #'.$p->id.' · Stock: '.$p->stock.' · '.($p->estado ? 'Activo' : 'Inactivo')])->all();

        return [$this->grupo('Productos asociados', 'Cambia la categoría de estos productos o revisa su estado antes de eliminar la categoría.', $total, $items, 'editar productos', 'productos.edit', 'ver productos', 'productos.index')];
    }

    public function paraProducto(int $id, int $tiendaId): array
    {
        $grupos = [];
        $q = DetalleFactura::query()->where('id_producto', $id)
            ->whereHas('producto', fn ($x) => $x->where('id_tienda', $tiendaId))
            ->whereHas('factura.usuario', fn ($x) => $x->where('id_tienda', $tiendaId))
            ->with(['factura', 'producto'])->orderBy('id');
        $total = (clone $q)->count();
        if ($total) {
            $items = (clone $q)->limit(self::LIMITE_ITEMS)->get()->map(fn ($d) => [
                'id' => $d->id_factura,
                'titulo' => 'Factura #'.$d->id_factura.' · Detalle #'.$d->id,
                'detalle' => ($d->producto?->nombre ?? 'Producto').' · Cantidad: '.$d->cantidad.' · Fecha: '.($d->factura?->fecha?->format('d/m/Y') ?? 'Sin fecha'),
            ])->all();
            $grupos[] = $this->grupo('Facturas asociadas', 'Este producto aparece en facturas registradas. Consulta cada factura para revisar sus productos y movimientos.', $total, $items, null, null, 'ver facturas', 'facturas.show');
        }

        $qMov = MovimientoInventario::query()->where('id_producto', $id)->where('id_tienda', $tiendaId)->orderByDesc('fecha');
        $totalMov = (clone $qMov)->count();
        if ($totalMov) {
            $items = (clone $qMov)->limit(self::LIMITE_ITEMS)->get(['id', 'tipo_movimiento', 'cantidad', 'fecha', 'motivo'])
                ->map(fn ($m) => [
                    'id' => $m->id,
                    'titulo' => 'Movimiento #'.$m->id.' · '.str_replace('_', ' ', ucfirst($m->tipo_movimiento)),
                    'detalle' => ($m->fecha?->format('d/m/Y H:i') ?? 'Sin fecha').' · Cantidad: '.($m->cantidad > 0 ? '+' : '').$m->cantidad.' · '.($m->motivo ?: 'Sin motivo registrado'),
                ])->all();
            if (auth()->user()?->can('gestionar seguridad')) {
                $items = array_map(function ($item) use ($id) {
                    $item['acciones'] = [[
                        'url' => route('reportes.index', ['producto' => $id]),
                        'texto' => 'Ver historial',
                    ]];

                    return $item;
                }, $items);
            }
            $grupos[] = $this->grupo('Historial de inventario', 'Estos movimientos son registros de auditoría y deben conservarse.', $totalMov, $items, null, null, 'gestionar seguridad', 'reportes.index');
        }

        return $grupos;
    }

    public function paraCliente(int $id, int $tiendaId): array
    {
        $q = Facturas::query()->where('id_cliente', $id)
            ->whereHas('usuario', fn ($x) => $x->where('id_tienda', $tiendaId))->orderByDesc('fecha');
        $total = (clone $q)->count();
        if (! $total) {
            return [];
        }
        $items = (clone $q)->limit(self::LIMITE_ITEMS)->get(['id', 'fecha', 'estado', 'total'])
            ->map(fn ($f) => [
                'id' => $f->id, 'titulo' => 'Factura #'.$f->id,
                'detalle' => 'Fecha: '.($f->fecha?->format('d/m/Y') ?? 'Sin fecha').' · Estado: '.ucfirst((string) $f->estado).' · Total: $'.number_format((float) $f->total, 2, ',', '.'),
            ])->all();

        return [$this->grupo('Facturas asociadas', 'El cliente tiene historial de facturación. Conserva su registro para mantener la trazabilidad de las ventas.', $total, $items, 'editar facturas', 'facturas.edit', 'ver facturas', 'facturas.index')];
    }

    public function paraFactura(int $id, int $tiendaId): array
    {
        $factura = Facturas::query()->whereHas('usuario', fn ($x) => $x->where('id_tienda', $tiendaId))->findOrFail($id);
        $grupos = [];
        $q = DetalleFactura::query()->where('id_factura', $factura->id)
            ->whereHas('factura.usuario', fn ($x) => $x->where('id_tienda', $tiendaId))
            ->whereHas('producto', fn ($x) => $x->where('id_tienda', $tiendaId))
            ->with('producto')->orderBy('id');
        $total = (clone $q)->count();
        if ($total) {
            $items = (clone $q)->limit(self::LIMITE_ITEMS)->get()->map(fn ($d) => [
                'id' => $factura->id,
                'titulo' => 'Detalle #'.$d->id.' · '.($d->producto?->nombre ?? 'Producto'),
                'detalle' => 'Cantidad: '.$d->cantidad.' · Subtotal:         }
        if ($factura->estado !== 'pendiente') {
            $grupos[] = ['titulo' => 'Condición de eliminación', 'descripcion' => 'Solo se pueden eliminar facturas pendientes. Las facturas pagadas o anuladas deben conservarse.', 'total' => 1, 'items' => [['titulo' => 'Estado actual: '.ucfirst((string) $factura->estado), 'detalle' => 'No se permite eliminar una factura en este estado.', 'acciones' => []]]];
        }

        return $grupos;
    }

    public function paraEmpleado(int $id, int $tiendaId): array
    {
        $q = Facturas::query()->where('id_usuario', $id)
            ->whereHas('usuario', fn ($x) => $x->where('id_tienda', $tiendaId))->orderByDesc('fecha');
        $total = (clone $q)->count();
        if (! $total) {
            return [];
        }
        $items = (clone $q)->limit(self::LIMITE_ITEMS)->get(['id', 'fecha', 'estado', 'total'])
            ->map(fn ($f) => [
                'id' => $f->id, 'titulo' => 'Factura #'.$f->id,
                'detalle' => 'Fecha: '.($f->fecha?->format('d/m/Y') ?? 'Sin fecha').' · Estado: '.ucfirst((string) $f->estado).' · Total: $'.number_format((float) $f->total, 2, ',', '.'),
            ])->all();

        return [$this->grupo('Facturas emitidas por el empleado', 'El historial debe conservarse; por eso la cuenta se desactiva en lugar de eliminarse.', $total, $items, 'editar facturas', 'facturas.edit', 'ver facturas', 'facturas.index')];
    }

    public function flash(string $titulo, string $mensaje, array $grupos): void
    {
        session()->flash('relaciones_bloqueo', ['titulo' => $titulo, 'mensaje' => $mensaje, 'grupos' => $grupos]);
    }

    private function grupo(string $titulo, string $descripcion, int $total, array $items, ?string $permisoEditar, ?string $rutaEditar, ?string $permisoVer, ?string $rutaVer): array
    {
        $user = auth()->user();
        $puedeEditar = $permisoEditar && $user?->can($permisoEditar);
        $puedeVer = $permisoVer && $user?->can($permisoVer);

        if (! $puedeEditar && ! $puedeVer) {
            return ['titulo' => $titulo, 'descripcion' => $descripcion.' Tu rol no tiene permiso para consultar el detalle de estos registros.', 'total' => $total, 'items' => []];
        }

        $items = array_map(function ($item) use ($puedeEditar, $rutaEditar, $puedeVer, $rutaVer) {
            if (! isset($item['acciones'])) {
                $item['acciones'] = [];

                if ($puedeEditar && $rutaEditar) {
                    $item['acciones'][] = [
                        'url' => route($rutaEditar, $item['id']),
                        'texto' => 'Editar',
                    ];
                }

                if ($puedeVer && $rutaVer) {
                    $item['acciones'][] = [
                        'url' => route($rutaVer),
                        'texto' => $rutaVer === 'facturas.show' || $rutaEditar === 'facturas.show' ? 'Consultar factura' : 'Abrir módulo',
                    ];
                }
            }

            return $item;
        }, $items);

        return ['titulo' => $titulo, 'descripcion' => $descripcion, 'total' => $total, 'items' => $items];
    }
}
.number_format((float) $d->subtotal, 2, ',', '.'),
            ])->all();
            $grupos[] = $this->grupo('Productos asociados', 'Consulta la factura para revisar sus productos y la información completa.', $total, $items, null, null, 'ver facturas', 'facturas.show');
        }
        if ($factura->estado !== 'pendiente') {
            $grupos[] = ['titulo' => 'Condición de eliminación', 'descripcion' => 'Solo se pueden eliminar facturas pendientes. Las facturas pagadas o anuladas deben conservarse.', 'total' => 1, 'items' => [['titulo' => 'Estado actual: '.ucfirst((string) $factura->estado), 'detalle' => 'No se permite eliminar una factura en este estado.', 'acciones' => []]]];
        }

        return $grupos;
    }

    public function paraEmpleado(int $id, int $tiendaId): array
    {
        $q = Facturas::query()->where('id_usuario', $id)
            ->whereHas('usuario', fn ($x) => $x->where('id_tienda', $tiendaId))->orderByDesc('fecha');
        $total = (clone $q)->count();
        if (! $total) {
            return [];
        }
        $items = (clone $q)->limit(self::LIMITE_ITEMS)->get(['id', 'fecha', 'estado', 'total'])
            ->map(fn ($f) => [
                'id' => $f->id, 'titulo' => 'Factura #'.$f->id,
                'detalle' => 'Fecha: '.($f->fecha?->format('d/m/Y') ?? 'Sin fecha').' · Estado: '.ucfirst((string) $f->estado).' · Total: $'.number_format((float) $f->total, 2, ',', '.'),
            ])->all();

        return [$this->grupo('Facturas emitidas por el empleado', 'El historial debe conservarse; por eso la cuenta se desactiva en lugar de eliminarse.', $total, $items, 'editar facturas', 'facturas.edit', 'ver facturas', 'facturas.index')];
    }

    public function flash(string $titulo, string $mensaje, array $grupos): void
    {
        session()->flash('relaciones_bloqueo', ['titulo' => $titulo, 'mensaje' => $mensaje, 'grupos' => $grupos]);
    }

    private function grupo(string $titulo, string $descripcion, int $total, array $items, ?string $permisoEditar, ?string $rutaEditar, ?string $permisoVer, ?string $rutaVer): array
    {
        $user = auth()->user();
        $puedeEditar = $permisoEditar && $user?->can($permisoEditar);
        $puedeVer = $permisoVer && $user?->can($permisoVer);

        if (! $puedeEditar && ! $puedeVer) {
            return ['titulo' => $titulo, 'descripcion' => $descripcion.' Tu rol no tiene permiso para consultar el detalle de estos registros.', 'total' => $total, 'items' => []];
        }

        $items = array_map(function ($item) use ($puedeEditar, $rutaEditar, $puedeVer, $rutaVer) {
            if (! isset($item['acciones'])) {
                $item['acciones'] = [];

                if ($puedeEditar && $rutaEditar) {
                    $item['acciones'][] = [
                        'url' => route($rutaEditar, $item['id']),
                        'texto' => 'Editar',
                    ];
                }

                if ($puedeVer && $rutaVer) {
                    $item['acciones'][] = [
                        'url' => route($rutaVer),
                        'texto' => 'Abrir módulo',
                    ];
                }
            }

            return $item;
        }, $items);

        return ['titulo' => $titulo, 'descripcion' => $descripcion, 'total' => $total, 'items' => $items];
    }
}
