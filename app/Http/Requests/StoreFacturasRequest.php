<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacturasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_cliente' => 'required|integer|exists:clientes,id',
            'fecha' => 'required|date',
            'estado' => 'required|in:pendiente,pagada,anulada',
        ];
    }
}
