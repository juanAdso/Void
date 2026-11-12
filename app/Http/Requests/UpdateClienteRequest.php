<?php

namespace App\Http\Requests;

use App\Models\Cliente;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $usuario = $this->user();
        $clienteId = $this->route('cliente') ?? $this->route('id');

        if (! $usuario || ! $usuario->estaActivo() || ! $clienteId) {
            return false;
        }

        if ($usuario->esSuperadministrador()) {
            return Cliente::query()->whereKey($clienteId)->exists();
        }

        return $usuario->id_tienda && Cliente::query()
            ->whereKey($clienteId)
            ->where('id_tienda', $usuario->id_tienda)
            ->exists();
    }

    public function rules(): array
    {
        $clienteId = $this->route('cliente') ?? $this->route('id');
        $cliente = Cliente::query()->findOrFail($clienteId);
        $tiendaId = (int) $cliente->id_tienda;

        return [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'documento' => [
                'required', 'string', 'max:20',
                Rule::unique('clientes', 'documento')
                    ->where(fn ($query) => $query->where('id_tienda', $tiendaId))
                    ->ignore($clienteId),
            ],
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:150',
            'direccion' => 'nullable|string|max:200',
        ];
    }
}
