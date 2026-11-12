<?php

namespace App\Models\Scopes;

use App\Models\DetalleFactura;
use App\Models\Facturas;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TiendaScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $usuario = auth()->user();

        // El portal público y los procesos de consola no tienen contexto tenant.
        if (! $usuario) {
            return;
        }

        // Solo la cuenta allowlisted con rol global puede consultar todas las tiendas.
        if ($usuario->esSuperadministrador()) {
            return;
        }

        $tiendaId = (int) ($usuario->id_tienda ?? 0);

        if ($tiendaId <= 0) {
            $builder->whereRaw('1 = 0');

            return;
        }

        if ($model instanceof Facturas) {
            $builder->whereHas(
                'usuario',
                fn (Builder $query) => $query->where('id_tienda', $tiendaId)
            );

            return;
        }

        if ($model instanceof DetalleFactura) {
            $builder->whereHas(
                'factura.usuario',
                fn (Builder $query) => $query->where('id_tienda', $tiendaId)
            );

            return;
        }

        $builder->where($model->qualifyColumn('id_tienda'), $tiendaId);
    }
}
