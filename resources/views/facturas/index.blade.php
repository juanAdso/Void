@extends('layouts.app')

@section('title', 'Facturas')

@section('content')
    <x-card>

        <div class="container mx-auto mt-10">

            <div class="bg-white shadow-lg rounded-lg p-6">

                <div class="flex justify-between items-center mb-6">

                    <h2 class="text-3xl font-bold text-gray-700">
                        Listado de Facturas
                    </h2>

                    <a href="{{ route('facturas.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">

                        Nueva Factura

                    </a>

                </div>

                @if (session('error'))
                <div class="mb-4 rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700">{{ session('error') }}</div>
            @endif

            @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">

                        {{ session('success') }}
                    </div>
                @endif

                @if (session('actualizar'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">

                        {{ session('actualizar') }}
                    </div>
                @endif

                @if (session('eliminar'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">

                        {{ session('eliminar') }}
                    </div>
                @endif

                <table class="min-w-full border border-gray-300">

                    <thead class="bg-gray-200">

                        <tr>

                            <th class="border px-4 py-2">
                                ID
                            </th>
                            <th class="border px-4 py-2">
                                Cliente
                            </th>
                            <th class="border px-4 py-2">
                                Fecha
                            </th>
                            <th class="border px-4 py-2">
                                Subtotal
                            </th>
                            <th class="border px-4 py-2">
                                Iva
                            </th>
                            <th class="border px-4 py-2">
                                Total
                            </th>
                            <th class="border px-4 py-2">
                                Estado
                            </th>
                            <th class="border px-4 py-2">
                                Acciones
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach ($facturas as $factura)
                            <tr class="text-center hover:bg-gray-50">
                                <td class="border px-4 py-2">{{ $factura->id }}</td>
                                <td class="border px-4 py-2">{{ $factura->cliente?->nombre ?? 'Cliente eliminado' }} {{ $factura->cliente?->apellido ?? '' }}</td>
                                <td class="border px-4 py-2">{{ $factura->fecha }}</td>
                                <td class="border px-4 py-2">{{ $factura->subtotal }}</td>
                                <td class="border px-4 py-2">{{ $factura->iva }}</td>
                                <td class="border px-4 py-2">{{ $factura->total }}</td>
                                <td class="border px-4 py-2">
                                    @if ($factura->estado === 'pagada')
                                        <span class="rounded bg-green-100 px-2 py-1 text-green-800">Pagada</span>
                                    @elseif ($factura->estado === 'pendiente')
                                        <span class="rounded bg-yellow-100 px-2 py-1 text-yellow-800">Pendiente</span>
                                    @else
                                        <span class="rounded bg-red-100 px-2 py-1 text-red-800">Anulada</span>
                                    @endif
                                </td>
                                
                                <td class="border px-4 py-2 flex justify-center gap-2">
                                    <a
                                        href="{{ route('facturas.edit', $factura->id) }}"class="bg-blue-400 hover:bg-blue-600 text-white rounded px-2 py-2">Editar</a>
                                    <form action="{{ route('facturas.destroy', $factura->id) }}" method="post">
                                
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-400 hover:bg-red-600 text-white rounded px-2 py-2">Eliminar</button>
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
