@extends('layouts.app')

@section('title', 'Ver Detalle de Factura')

@section('content')
    <div class="container mx-auto mt-10">
        <div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg p-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-gray-700">Detalle de Factura #{{ $detalle_factura->id }}</h2>
                <a href="{{ route('detalle_facturas.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded">
                    Volver
                </a>
            </div>

            <div class="space-y-4 border-t pt-4">
                <div>
                    <span class="font-bold">Factura ID:</span>
                    <span>#{{ $detalle_factura->id_factura }}</span>
                </div>
                <div>
                    <span class="font-bold">Producto:</span>
                    <span>{{ $detalle_factura->producto ? $detalle_factura->producto->nombre : 'N/A' }}</span>
                </div>
                <div>
                    <span class="font-bold">Cantidad:</span>
                    <span>{{ $detalle_factura->cantidad }}</span>
                </div>
                <div>
                    <span class="font-bold">Precio Unitario:</span>
                    <span>${{ number_format($detalle_factura->precio_unitario, 2) }}</span>
                </div>
                <div>
                    <span class="font-bold">Subtotal:</span>
                    <span>${{ number_format($detalle_factura->subtotal, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
