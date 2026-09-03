<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
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
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'documento' => 'required|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|string|email|max:150',
            'direccion' => 'nullable|string|max:200',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no puede tener más de 100 caracteres.',
            'apellido.required' => 'El apellido es obligatorio.',
            'apellido.string' => 'El apellido debe ser una cadena de texto.',
            'apellido.max' => 'El apellido no puede tener más de 100 caracteres.',
            'documento.required' => 'El documento es obligatorio.',
            'documento.string' => 'El documento debe ser una cadena de texto.',
            'documento.max' => 'El documento no puede tener más de 20 caracteres.',
            'telefono.string' => 'El teléfono debe ser una cadena de texto.',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'correo.string' => 'El correo debe ser una cadena de texto.',
            'correo.email' => 'El correo debe ser una dirección de correo válida.',
            'correo.max' => 'El correo no puede tener más de 150 caracteres.',
            'direccion.string' => 'La dirección debe ser una cadena de texto.',
            'direccion.max' => 'La dirección no puede tener más de 200 caracteres.',
        ];
    }
}
