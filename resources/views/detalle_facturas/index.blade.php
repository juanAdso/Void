@extends('layouts.app')

@section('title', 'Detalle Facturas')

@section('content')
<div class="container mx-auto mt-10">

    <div class="bg-white shadow-lg rounded-lg p-6">

        <div class="flex justify-between items-center mb-6">

            <h2 class="text-3xl font-bold text-gray-700">
                Detalle de Facturas
            </h2>

            <a href="{{ route('detalle_facturas.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">

                Nuevo Detalle de Factura
            </a>

        </div>

        @if (session('success'))
            <div class="mb-4 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded border border-red-400 bg-red-100 px-4 py-3 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <table class="min-w-full border border-gray-200">

            <thead class="bg-gray-200">

                <tr>

                    <th class="border px-4 py-2">
                        ID
                    </th>

                    <th class="border px-4 py-2">
                        Factura
                    </th>

                    <th class="border px-4 py-2">
                        Producto
                    </th>

                    <th class="border px-4 py-2">
                        Cantidad
                    </th>

                    <th class="border px-4 py-2">
                        Precio Unitario
                    </th>

                    <th class="border px-4 py-2">
                        Subtotal
                    </th>

                    <th class="border px-4 py-2">
                        Acciones
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach ($detalle_facturas as $detalle_factura)
                    <tr class="text-center hover:bg-gray-50">
                        <td class="border px-4 py-2">{{ $detalle_factura->id }}</td>
                        <td class="border px-4 py-2">{{ $detalle_factura->factura?->id }}</td>
                        <td class="border px-4 py-2">{{ $detalle_factura->producto?->nombre }}</td>
                        <td class="border px-4 py-2">{{ $detalle_factura->cantidad }}</td>
                        <td class="border px-4 py-2">{{ $detalle_factura->precio_unitario }}</td>
                        <td class="border px-4 py-2">{{ $detalle_factura->subtotal }}</td>

                        <td class="border px-4 py-2 flex justify-center gap-2">
                            <a href="{{ route('detalle_facturas.edit', $detalle_factura->id) }}"
                                class="bg-purple-400 hover:bg-purple-600 text-white rounded px-2 py-2">Editar</a>

                            <form action="{{ route('detalle_facturas.destroy', $detalle_factura->id) }}"
                                method="post" class="inline-block">
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
@endsection
