<?php

namespace App\Http\Requests;

use App\Models\DetalleFactura;
use App\Models\Facturas;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDetalleFacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $usuario = $this->user();
        $detalleId = $this->route('detalle_factura');

        if (! $usuario || ! $usuario->estaActivo() || ! $detalleId) {
            return false;
        }

        if ($usuario->esSuperadministrador()) {
            return DetalleFactura::query()->whereKey($detalleId)->exists();
        }

        return $usuario->id_tienda && DetalleFactura::query()
            ->whereKey($detalleId)
            ->whereHas('factura.usuario', fn ($query) => $query->where('id_tienda', $usuario->id_tienda))
            ->exists();
    }

    public function rules(): array
    {
        $detalleId = $this->route('detalle_factura');
        $detalle = DetalleFactura::query()->with('factura.usuario')->findOrFail($detalleId);
        $tiendaId = (int) $detalle->factura->usuario->id_tienda;

        return [
            'id_factura' => [
                'required',
                'integer',
                Rule::exists('facturas', 'id')->where(function ($query) use ($tiendaId) {
                    $query->where('estado', 'pendiente')
                        ->whereIn('id_usuario', function ($subQuery) use ($tiendaId) {
                            $subQuery->select('id')->from('usuarios')->where('id_tienda', $tiendaId);
                        });
                }),
            ],
            'id_producto' => [
                'required',
                'integer',
                Rule::exists('productos', 'id')->where(fn ($query) => $query->where('id_tienda', $tiendaId)),
            ],
            'cantidad' => ['required', 'integer', 'min:1'],
            'precio_unitario' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_factura.exists' => 'La factura seleccionada debe estar pendiente y pertenecer a la misma tienda del detalle.',
            'id_producto.exists' => 'El producto seleccionado debe pertenecer a la misma tienda del detalle.',
        ];
    }
}
