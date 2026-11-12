<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->id_tienda;
    }

    public function rules(): array
    {
        $tiendaId = $this->user()->id_tienda;

        return [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'documento' => [
                'required', 'string', 'max:20',
                Rule::unique('clientes', 'documento')->where(fn ($query) => $query->where('id_tienda', $tiendaId)),
            ],
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:150',
            'direccion' => 'nullable|string|max:200',
        ];
    }

    public function messages(): array
    {
        return [
            'documento.unique' => 'Ya existe un cliente con ese documento en tu tienda.',
        ];
    }
}
