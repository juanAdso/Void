<?php

namespace App\Http\Requests;

use App\Models\Facturas;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuardarCambiosFacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $usuario = $this->user();

        if (! $usuario?->id_tienda) {
            return false;
        }

        $factura = Facturas::query()
            ->whereKey($this->route('factura'))
            ->whereHas('usuario', fn ($query) => $query->where('id_tienda', $usuario->id_tienda))
            ->first();

        if (! $factura || $factura->estado === 'anulada') {
            return false;
        }

        return collect([
            'editar facturas',
            'editar detallefacturas',
            'crear detallefacturas',
            'eliminar detallefacturas',
        ])->contains(fn ($permiso) => $usuario->tienePermisoActivo($permiso));
    }

    public function rules(): array
    {
        $usuario = $this->user();
        $tiendaId = (int) $usuario->id_tienda;
        $puedeEditarFactura = $usuario->tienePermisoActivo('editar facturas');
        $puedeEditarDetalles = $usuario->tienePermisoActivo('editar detallefacturas');
        $puedeCrearDetalles = $usuario->tienePermisoActivo('crear detallefacturas');
        $puedeEliminarDetalles = $usuario->tienePermisoActivo('eliminar detallefacturas');
        $puedeGestionarDetalles = $puedeEditarDetalles || $puedeCrearDetalles;

        $reglaIdDetalle = match (true) {
            $puedeEditarDetalles && $puedeCrearDetalles => 'nullable',
            $puedeEditarDetalles => 'required',
            default => 'prohibited',
        };

        return [
            'id_cliente' => [
                $puedeEditarFactura ? 'required' : 'prohibited',
                'integer',
                Rule::exists('clientes', 'id')->where(fn ($query) => $query->where('id_tienda', $tiendaId)),
            ],
            'fecha' => [$puedeEditarFactura ? 'required' : 'prohibited', 'date'],
            'estado' => [
                $puedeEditarFactura ? 'required' : 'prohibited',
                Rule::in(['pendiente', 'pagada', 'anulada']),
            ],
            'detalles' => [
                $puedeGestionarDetalles ? 'sometimes' : 'prohibited',
                'array',
                'max:100',
            ],
            'detalles.*.id' => [$reglaIdDetalle, 'integer'],
            'detalles.*.id_producto' => [
                'required',
                'integer',
                Rule::exists('productos', 'id')->where(fn ($query) => $query->where('id_tienda', $tiendaId)),
            ],
            'detalles.*.cantidad' => ['required', 'integer', 'min:1'],
            'detalles.*.precio_unitario' => ['required', 'numeric', 'min:0'],
            'eliminar_detalles' => [
                $puedeEliminarDetalles ? 'sometimes' : 'prohibited',
                'array',
                'max:100',
            ],
            'eliminar_detalles.*' => ['integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_cliente.exists' => 'El cliente seleccionado no existe o no pertenece a tu tienda.',
            'detalles.*.id_producto.exists' => 'Uno de los productos no pertenece a tu tienda.',
            'detalles.*.cantidad.min' => 'Cada producto debe tener una cantidad mayor que cero.',
            'eliminar_detalles.*.integer' => 'Uno de los detalles que intentas retirar no es válido.',
        ];
    }
}
