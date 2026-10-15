<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFacturasRequest extends FormRequest
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
            'id_cliente' => 'required|integer|exists:clientes,id',
            'fecha' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'iva' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'estado' => 'required|string|max:30',
        ];
    }

    public function messages(): array
    {
        return [
            'id_cliente.required' => 'El campo id_cliente es obligatorio.',
            'id_cliente.integer' => 'El campo id_cliente debe ser un número entero.',
            'id_cliente.exists' => 'El cliente especificado no existe.',
            'fecha.required' => 'El campo fecha es obligatorio.',
            'fecha.date' => 'El campo fecha debe ser una fecha válida.',
            'subtotal.required' => 'El campo subtotal es obligatorio.',
            'subtotal.numeric' => 'El campo subtotal debe ser un número.',
            'subtotal.min' => 'El campo subtotal debe ser mayor o igual a 0.',
            'iva.required' => 'El campo iva es obligatorio.',
            'iva.numeric' => 'El campo iva debe ser un número.',
            'iva.min' => 'El campo iva debe ser mayor o igual a 0.',
            'total.required' => 'El campo total es obligatorio.',
            'total.numeric' => 'El campo total debe ser un número.',
            'total.min' => 'El campo total debe ser mayor o igual a 0.',
            'estado.required' => 'El campo estado es obligatorio.',
            'estado.string' => 'El campo estado debe ser una cadena de texto.',
            'estado.max' => 'El campo estado no puede tener más de 30 caracteres.',
        ];
    }
}
