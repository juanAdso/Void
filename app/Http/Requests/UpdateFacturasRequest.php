<?php

namespace App\Http\Requests;

use App\Models\Facturas;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFacturasRequest extends FormRequest
{
    public function authorize(): bool
    {
        $usuario = $this->user();
        $facturaId = $this->route('factura');

        if (! $usuario || ! $usuario->estaActivo() || ! $facturaId) {
            return false;
        }

        if ($usuario->esSuperadministrador()) {
            return Facturas::query()->whereKey($facturaId)->exists();
        }

        return $usuario->id_tienda && Facturas::query()
            ->whereKey($facturaId)
            ->whereHas('usuario', fn ($query) => $query->where('id_tienda', $usuario->id_tienda))
            ->exists();
    }

    public function rules(): array
    {
        $facturaId = $this->route('factura');
        $factura = Facturas::query()->with('usuario')->findOrFail($facturaId);
        $tiendaId = (int) $factura->usuario->id_tienda;

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
            'id_cliente.exists' => 'El cliente seleccionado no existe o no pertenece a la tienda de la factura.',
        ];
    }
}
