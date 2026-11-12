<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->id_tienda;
    }

    public function rules(): array
    {
        $tiendaId = $this->user()->id_tienda;
        $clienteId = $this->route('cliente') ?? $this->route('id');

        return [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'documento' => [
                'required', 'string', 'max:20',
                Rule::unique('clientes', 'documento')
                    ->where(fn ($query) => $query->where('id_tienda', $tiendaId))
                    ->ignore($clienteId),
            ],
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:150',
            'direccion' => 'nullable|string|max:200',
        ];
    }
}
