<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $admin = $this->user();
        $empleadoId = $this->route('empleado') ?? $this->route('id');

        return (bool) $admin?->id_tienda
            && $admin->hasRole('administrador')
            && $admin->can('gestionar seguridad')
            && User::query()
                ->where('id_tienda', $admin->id_tienda)
                ->where('rol', 'empleado')
                ->whereKey($empleadoId)
                ->exists();
    }

    public function rules(): array
    {
        $empleadoId = $this->route('empleado') ?? $this->route('id');

        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'documento' => [
                'required', 'string', 'max:20',
                Rule::unique('usuarios', 'documento')->ignore($empleadoId),
            ],
            'correo' => [
                'required', 'email', 'max:150',
                Rule::unique('usuarios', 'correo')->ignore($empleadoId),
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'estado' => ['required', 'boolean'],
        ];
    }
}
