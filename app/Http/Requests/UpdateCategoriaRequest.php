<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoriaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre_categoria' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_categoria.required' => 'El nombre de la categoría es obligatorio.',
            'nombre_categoria.string' => 'El nombre de la categoría debe ser una cadena de texto.',
            'nombre_categoria.max' => 'El nombre de la categoría no puede tener más de 255 caracteres.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }
}
