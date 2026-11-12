<?php

namespace App\Repositories;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Facturas;
use App\Models\Productos;
use App\Models\Tienda;
use App\Models\User;
use App\Repositories\Interfaces\BusquedaRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class BusquedaRepository implements BusquedaRepositoryInterface
{
    public function buscar(User $usuario, string $termino): array
    {
        $tiendaId = (int) $usuario->id_tienda;

        if ($tiendaId <= 0) {
            return $this->resultadosVacios();
        }

        $patron = '%'.$termino.'%';

        return [
            'productos' => $usuario->can('ver productos')
                ? Productos::query()
                    ->where('id_tienda', $tiendaId)
                    ->where(function (Builder $consulta) use ($patron) {
                        $consulta->where('nombre', 'like', $patron)
                            ->orWhere('descripcion', 'like', $patron);
                    })
                    ->orderBy('nombre')
                    ->limit(8)
                    ->get()
                : collect(),

            'categorias' => $usuario->can('ver categorias')
                ? Categoria::query()
                    ->where('id_tienda', $tiendaId)
                    ->where(function (Builder $consulta) use ($patron) {
                        $consulta->where('nombre', 'like', $patron)
                            ->orWhere('descripcion', 'like', $patron);
                    })
                    ->orderBy('nombre')
                    ->limit(8)
                    ->get()
                : collect(),

            'clientes' => $usuario->can('ver clientes')
                ? Cliente::query()
                    ->where('id_tienda', $tiendaId)
                    ->where(function (Builder $consulta) use ($patron) {
                        $consulta->where('nombre', 'like', $patron)
                            ->orWhere('apellido', 'like', $patron)
                            ->orWhere('documento', 'like', $patron)
                            ->orWhere('correo', 'like', $patron)
                            ->orWhere('telefono', 'like', $patron);
                    })
                    ->orderBy('nombre')
                    ->orderBy('apellido')
                    ->limit(8)
                    ->get()
                : collect(),

            'facturas' => $usuario->can('ver facturas')
                ? Facturas::query()
                    ->with('cliente')
                    ->whereHas('usuario', fn (Builder $consulta) => $consulta->where('id_tienda', $tiendaId))
                    ->where(function (Builder $consulta) use ($termino, $patron, $tiendaId) {
                        if (ctype_digit($termino)) {
                            $consulta->where('id', (int) $termino);
                        } else {
                            $consulta->where('estado', 'like', $patron);
                        }

                        $consulta->orWhere('fecha', 'like', $patron)
                            ->orWhereHas('cliente', function (Builder $cliente) use ($patron, $tiendaId) {
                                $cliente->where('id_tienda', $tiendaId)
                                    ->where(function (Builder $datosCliente) use ($patron) {
                                        $datosCliente->where('nombre', 'like', $patron)
                                            ->orWhere('apellido', 'like', $patron)
                                            ->orWhere('documento', 'like', $patron);
                                    });
                            });
                    })
                    ->orderByDesc('fecha')
                    ->limit(8)
                    ->get()
                : collect(),

            'empleados' => $usuario->can('gestionar seguridad')
                ? User::query()
                    ->where('id_tienda', $tiendaId)
                    ->where('rol', 'empleado')
                    ->where(function (Builder $consulta) use ($patron) {
                        $consulta->where('nombre', 'like', $patron)
                            ->orWhere('apellido', 'like', $patron)
                            ->orWhere('documento', 'like', $patron)
                            ->orWhere('correo', 'like', $patron);
                    })
                    ->orderBy('nombre')
                    ->orderBy('apellido')
                    ->limit(8)
                    ->get()
                : collect(),

            'tiendas' => $usuario->can('ver tiendas')
                ? Tienda::query()
                    ->whereKey($tiendaId)
                    ->where(function (Builder $consulta) use ($patron) {
                        $consulta->where('nombre', 'like', $patron)
                            ->orWhere('descripcion', 'like', $patron)
                            ->orWhere('direccion', 'like', $patron)
                            ->orWhere('telefono', 'like', $patron)
                            ->orWhere('correo', 'like', $patron);
                    })
                    ->limit(1)
                    ->get()
                : collect(),
        ];
    }

    private function resultadosVacios(): array
    {
        return [
            'productos' => collect(),
            'categorias' => collect(),
            'clientes' => collect(),
            'facturas' => collect(),
            'empleados' => collect(),
            'tiendas' => collect(),
        ];
    }
}
