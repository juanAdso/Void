<?php

namespace App\Http\Requests;

use App\Models\Categoria;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $usuario = $this->user();
        $categoriaId = $this->route('categorium') ?? $this->route('categoria') ?? $this->route('id');

        if (! $usuario || ! $usuario->estaActivo() || ! $categoriaId) {
            return false;
        }

        if ($usuario->esSuperadministrador()) {
            return Categoria::query()->whereKey($categoriaId)->exists();
        }

        return $usuario->id_tienda && Categoria::query()
            ->whereKey($categoriaId)
            ->where('id_tienda', $usuario->id_tienda)
            ->exists();
    }

    public function rules(): array
    {
        $categoriaId = $this->route('categorium') ?? $this->route('categoria') ?? $this->route('id');
        $categoria = Categoria::query()->findOrFail($categoriaId);
        $tiendaId = (int) $categoria->id_tienda;

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
