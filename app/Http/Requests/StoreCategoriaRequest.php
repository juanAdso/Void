<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->id_tienda;
    }

    public function rules(): array
    {
        $tiendaId = $this->user()->id_tienda;

        return [
            'nombre' => [
                'required', 'string', 'max:100',
                Rule::unique('categorias', 'nombre')->where(fn ($query) => $query->where('id_tienda', $tiendaId)),
            ],
            'descripcion' => 'nullable|string|max:255',
            'estado' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.unique' => 'Ya existe una categoría con ese nombre en tu tienda.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }
}
