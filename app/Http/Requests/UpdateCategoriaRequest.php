<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->id_tienda;
    }

    public function rules(): array
    {
        $tiendaId = $this->user()->id_tienda;
        $categoriaId = $this->route('categorium') ?? $this->route('categoria') ?? $this->route('id');

        return [
            'nombre' => [
                'required', 'string', 'max:100',
                Rule::unique('categorias', 'nombre')
                    ->where(fn ($query) => $query->where('id_tienda', $tiendaId))
                    ->ignore($categoriaId),
            ],
            'descripcion' => 'nullable|string|max:255',
            'estado' => 'required|boolean',
        ];
    }
}
