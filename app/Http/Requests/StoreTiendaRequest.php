<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTiendaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:100',
            'estado' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la tienda es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 100 caracteres.',
            'logo.image' => 'El logo debe ser una imagen válida.',
            'logo.mimes' => 'El logo debe estar en formato JPG, JPEG, PNG o WEBP.',
            'logo.max' => 'El logo no puede superar los 2 MB.',
            'telefono.max' => 'El teléfono no puede superar los 20 caracteres.',
            'correo.email' => 'Ingresa un correo electrónico válido.',
            'correo.max' => 'El correo no puede superar los 100 caracteres.',
            'estado.required' => 'El estado de la tienda es obligatorio.',
            'estado.boolean' => 'El estado debe ser activo o inactivo.',
        ];
    }
}
