<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFacturasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_cliente' => 'required|integer|exists:clientes,id_cliente',
            'id_usuario' => 'required|integer|exists:usuarios,id_usuario',
            'fecha' => 'required|date',
            'estado' => 'required|in:pendiente,pagada,anulada',
        ];
    }
}
