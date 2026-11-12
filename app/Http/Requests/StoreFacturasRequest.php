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
        ];
    }

    public function messages(): array
    {
        return [
            'id_cliente.exists' => 'El cliente seleccionado no existe o no pertenece a tu tienda.',
            'estado.in' => 'Las facturas deben crearse en estado pendiente.',
        ];
    }
}
