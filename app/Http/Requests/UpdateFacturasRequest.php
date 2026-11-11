<?php

namespace App\Http\Requests;

use App\Models\Facturas;
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
            'id_cliente' => 'required|integer|exists:clientes,id',
            'fecha' => 'required|date',
            'estado' => 'required|in:pendiente,pagada,anulada',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $id = $this->route('factura');
            if (! $id) {
                return;
            }

            $factura = Facturas::find($id);
            if (! $factura) {
                return;
            }

            $nuevoEstado = $this->input('estado');

            if ($factura->estado === 'anulada') {
                $validator->errors()->add('estado', 'Una factura anulada es inmutable y no se puede modificar.');

                return;
            }

            if ($factura->estado === 'pagada') {
                if ($nuevoEstado === 'pendiente') {
                    $validator->errors()->add('estado', 'Una factura pagada no puede regresar a estado pendiente.');
                }
                if ((int) $this->input('id_cliente') !== (int) $factura->id_cliente) {
                    $validator->errors()->add('id_cliente', 'No se puede cambiar el cliente de una factura pagada.');
                }
                $fechaEnviada = $this->input('fecha');
                $fechaActual = $factura->fecha?->format('Y-m-d') ?? $factura->fecha?->toDateString();
                if ($fechaEnviada !== $fechaActual) {
                    $validator->errors()->add('fecha', 'No se puede modificar la fecha de una factura pagada.');
                }
            }
        });
    }
}
