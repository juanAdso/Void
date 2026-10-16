@extends('layouts.app')

@section('title', 'Detalle de Factura')

@section('content')
    @php
        $factura = $factura ?? $facturas;
    @endphp
    <div class="container mx-auto mt-10">
        <div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg p-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-gray-700">Factura #{{ $factura->id }}</h2>
                <a href="{{ route('facturas.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded">
                    Volver
                </a>
            </div>

            <div class="space-y-4 border-t pt-4">
                <div>
                    <span class="font-bold">Cliente:</span>
                    <span>{{ $factura->cliente ? $factura->cliente->nombre . ' ' . $factura->cliente->apellido : 'N/A' }}</span>
                </div>
                <div>
                    <span class="font-bold">Fecha:</span>
                    <span>{{ $factura->fecha ? $factura->fecha->format('Y-m-d') : '' }}</span>
                </div>
                <div>
                    <span class="font-bold">Subtotal:</span>
                    <span>${{ number_format($factura->subtotal, 2) }}</span>
                </div>
                <div>
                    <span class="font-bold">IVA:</span>
                    <span>${{ number_format($factura->iva, 2) }}</span>
                </div>
                <div>
                    <span class="font-bold">Total:</span>
                    <span>${{ number_format($factura->total, 2) }}</span>
                </div>
                <div>
                    <span class="font-bold">Estado:</span>
                    <span class="px-2 py-1 rounded text-xs text-white {{ $factura->estado === 'Pagada' ? 'bg-green-500' : 'bg-yellow-500' }}">
                        {{ $factura->estado }}
                    </span>
                </div>
            </div>
        </div>
    </div>
@endsection
