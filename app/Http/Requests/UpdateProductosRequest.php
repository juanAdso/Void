<?php

namespace App\Http\Requests;

use App\Models\Productos;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductosRequest extends FormRequest
{
    public function authorize(): bool
    {
        $usuario = $this->user();
        $productoId = $this->route('producto') ?? $this->route('id');

        if (! $usuario || ! $usuario->estaActivo() || ! $productoId) {
            return false;
        }

        if ($usuario->esSuperadministrador()) {
            return Productos::query()->whereKey($productoId)->exists();
        }

        return $usuario->id_tienda && Productos::query()
            ->whereKey($productoId)
            ->where('id_tienda', $usuario->id_tienda)
            ->exists();
    }

    public function rules(): array
    {
        $productoId = $this->route('producto') ?? $this->route('id');
        $producto = Productos::query()->findOrFail($productoId);
        $tiendaId = (int) $producto->id_tienda;

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
            'id_categoria.exists' => 'La categoría seleccionada no pertenece a la tienda del producto.',
        ];
    }
}
