<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFacturasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->id_tienda;
    }

    public function rules(): array
    {
        $tiendaId = $this->user()->id_tienda;

        return [
            'id_cliente' => [
                'required',
                'integer',
                Rule::exists('clientes', 'id')->where(fn ($query) => $query->where('id_tienda', $tiendaId)),
            ],
            'fecha' => ['required', 'date'],
            'estado' => ['required', Rule::in(['pendiente'])],
            'detalles' => ['sometimes', 'array', 'max:100'],
            'detalles.*.id_producto' => [
                'required',
                'integer',
                Rule::exists('productos', 'id')->where(
                    fn ($query) => $query->where('id_tienda', $tiendaId)->where('estado', true)
                ),
            ],
            'detalles.*.cantidad' => ['required', 'integer', 'min:1'],
            'detalles.*.precio_unitario' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_cliente.exists' => 'El cliente seleccionado no existe o no pertenece a tu tienda.',
            'estado.in' => 'Las facturas deben crearse en estado pendiente.',
            'detalles.*.id_producto.exists' => 'Uno de los productos no pertenece a tu tienda o no está activo.',
            'detalles.*.cantidad.min' => 'Cada producto debe tener una cantidad mayor que cero.',
            'detalles.*.cantidad.required' => 'Indica la cantidad de cada producto.',
            'detalles.*.precio_unitario.required' => 'Indica el precio unitario de cada producto.',
        ];
    }
}
