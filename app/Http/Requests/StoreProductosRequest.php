<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductosRequest extends FormRequest
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
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'estado' => 'required|boolean',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen' => 'nullable|image',    
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.string' => 'El nombre del producto debe ser una cadena de texto.',
            'nombre.max' => 'El nombre del producto no puede tener más de 255 caracteres.',
            'descripcion.string' => 'La descripción del producto debe ser una cadena de texto.',
            'precio.required' => 'El precio del producto es obligatorio.',
            'precio.numeric' => 'El precio del producto debe ser un número.',
            'precio.min' => 'El precio del producto no puede ser negativo.',
            'stock.required' => 'El stock del producto es obligatorio.',
            'stock.integer' => 'El stock del producto debe ser un número entero.',
            'stock.min' => 'El stock del producto no puede ser negativo.',
            'categoria_id.required' => 'La categoría del producto es obligatoria.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
            'estado.required' => 'El estado del producto es obligatorio.',
            'estado.boolean' => 'El estado del producto debe ser un valor booleano.',
            'imagen.required' => 'La imagen del producto es obligatorio.',
            'imagen.string' => 'La imagen del producto debe ser una cadena de texto.',
            'imagen.max' => 'La imagene del producto no puede tener más de 255 caracteres.',
        ];
    }
}
