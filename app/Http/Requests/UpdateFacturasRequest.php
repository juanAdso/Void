<?php

namespace App\Http\Requests;

use App\Models\Facturas;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFacturasRequest extends FormRequest
{
    public function authorize(): bool
    {
        $tiendaId = $this->user()?->id_tienda;
        $facturaId = $this->route('factura');

        if (! $tiendaId || ! $facturaId) {
            return false;
        }

        return Facturas::query()
            ->whereKey($facturaId)
            ->whereHas('usuario', fn ($query) => $query->where('id_tienda', $tiendaId))
            ->exists();
    }

    public function rules(): array
    {
        $tiendaId = $this->user()->id_tienda;

        return [
            'id_cliente' => [
                'required',
                'integer',
                Rule::exists('clientes', 'id')->where(fn ($query) => $query->where('id_tienda', $tiendaId)),
            ],
            'fecha' => ['required', 'date'],
            'estado' => ['required', Rule::in(['pendiente', 'pagada', 'anulada'])],
        ];
    }

    public function messages(): array
    {
        return [
            'id_cliente.exists' => 'El cliente seleccionado no existe o no pertenece a tu tienda.',
        ];
    }
}
