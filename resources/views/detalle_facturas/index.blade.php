@extends('layouts.app')

@section('title', 'Detalle de Facturas')

@section('content')
    <x-card>
        <div class="container mx-auto mt-10">
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold text-gray-700">Listado de Detalles de Factura</h2>
                    <a href="{{ route('detalle_facturas.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        Nuevo Detalle
                    </a>
                </div>

                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <table class="min-w-full border border-gray-300">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border px-4 py-2">ID</th>
                            <th class="border px-4 py-2">Factura #</th>
                            <th class="border px-4 py-2">Producto</th>
                            <th class="border px-4 py-2">Cantidad</th>
                            <th class="border px-4 py-2">Precio Unitario</th>
                            <th class="border px-4 py-2">Subtotal</th>
                            <th class="border px-4 py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detalle_facturas as $detalle)
                            <tr class="text-center hover:bg-gray-50">
                                <td class="border px-4 py-2">{{ $detalle->id }}</td>
                                <td class="border px-4 py-2">#{{ $detalle->id_factura }}</td>
                                <td class="border px-4 py-2">{{ $detalle->producto ? $detalle->producto->nombre : 'Sin producto' }}</td>
                                <td class="border px-4 py-2">{{ $detalle->cantidad }}</td>
                                <td class="border px-4 py-2">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                <td class="border px-4 py-2">${{ number_format($detalle->subtotal, 2) }}</td>
                                <td class="border px-4 py-2 flex justify-center gap-2">
                                    <a href="{{ route('detalle_facturas.show', $detalle->id) }}" class="bg-gray-500 hover:bg-gray-700 text-white rounded px-2 py-2">Ver</a>
                                    <a href="{{ route('detalle_facturas.edit', $detalle->id) }}" class="bg-blue-400 hover:bg-blue-600 text-white rounded px-2 py-2">Editar</a>
                                    <form action="{{ route('detalle_facturas.destroy', $detalle->id) }}" method="post" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-400 hover:bg-red-600 text-white rounded px-2 py-2">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </x-card>
@endsection
