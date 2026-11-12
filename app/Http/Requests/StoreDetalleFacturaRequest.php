<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDetalleFacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->id_tienda;
    }

    public function rules(): array
    {
        $tiendaId = $this->user()->id_tienda;

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
            'id_factura.exists' => 'La factura seleccionada no pertenece a tu tienda, no existe o no está pendiente.',
            'id_producto.exists' => 'El producto seleccionado no pertenece a tu tienda o no es válido.',
        ];
    }
}
