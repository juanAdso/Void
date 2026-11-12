<?php

namespace App\Http\Requests;

use App\Models\Productos;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductosRequest extends FormRequest
{
    public function authorize(): bool
    {
        $tiendaId = $this->user()?->id_tienda;
        $productoId = $this->route('producto') ?? $this->route('id');

        return $tiendaId && $productoId && Productos::query()
            ->whereKey($productoId)
            ->where('id_tienda', $tiendaId)
            ->exists();
    }

    public function rules(): array
    {
        $tiendaId = $this->user()->id_tienda;

        return [
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'estado' => ['required', 'boolean'],
            'id_categoria' => [
                'required',
                'integer',
                Rule::exists('categorias', 'id')->where(fn ($query) => $query->where('id_tienda', $tiendaId)),
            ],
            'imagen' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_categoria.exists' => 'La categoría seleccionada no pertenece a tu tienda.',
        ];
    }
}
