<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $empleadoId = $this->route('empleado') ?? $this->route('id');

        return [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'documento' => [
                'required',
                'string',
                'max:20',
                Rule::unique('usuarios', 'documento')->ignore($empleadoId),
            ],
            'correo' => [
                'required',
                'email',
                'max:150',
                Rule::unique('usuarios', 'correo')->ignore($empleadoId),
            ],
            'password' => 'nullable|string|min:6',
            'estado' => 'required|boolean',
        ];
    }
}
