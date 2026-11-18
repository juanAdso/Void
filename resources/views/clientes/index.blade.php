@extends('layouts.app')
@section('page-title', 'Gestión de clientes')
@section('title', 'Clientes')
@section('content')
    @php
        $mostrarAcciones = auth()
            ->user()
            ->canAny(['editar clientes', 'eliminar clientes']);
    @endphp
    <x-card>
        <div class="container mx-auto mt-10">
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-2xl font-bold text-gray-800">Listado de Clientes</h2>
                    @can('crear clientes')
                        <a href="{{ route('cliente.create') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Nuevo Cliente</a>
                    @endcan
                </div>
                @if (session('error'))
                    <div class="mb-4 rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700">{{ session('error') }}
                    </div>
                @endif
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}</div>
                @endif
                @if (session('actualizar'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('actualizar') }}</div>
                @endif
                @if (session('eliminar'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('eliminar') }}</div>
                @endif
                <div class="w-full max-w-full overflow-x-auto rounded-lg border border-slate-200">
                    <table class="datatable min-w-full w-full border border-gray-300">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border px-4 py-2">ID</th>
                                <th class="border px-4 py-2">Nombre</th>
                                <th class="border px-4 py-2">Apellido</th>
                                <th class="border px-4 py-2">Documento</th>
                                <th class="border px-4 py-2">Telefono</th>
                                <th class="border px-4 py-2">Correo</th>
                                <th class="border px-4 py-2">Dirección</th>
                                @if ($mostrarAcciones)
                                    <th class="border px-4 py-2" data-searchable="false" data-orderable="false">Acciones
                                    </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clientes as $cliente)
                                <tr class="text-center hover:bg-gray-50">
                                    <td class="border px-4 py-2">{{ $cliente->id }}</td>
                                    <td class="border px-4 py-2">{{ $cliente->nombre }}</td>
                                    <td class="border px-4 py-2">{{ $cliente->apellido }}</td>
                                    <td class="border px-4 py-2">{{ $cliente->documento }}</td>
                                    <td class="border px-4 py-2">{{ $cliente->telefono }}</td>
                                    <td class="border px-4 py-2">{{ $cliente->correo }}</td>
                                    <td class="border px-4 py-2">{{ $cliente->direccion }}</td>
                                    @if ($mostrarAcciones)
                                        <td class="border px-4 py-2">
                                            <div class="flex min-w-[180px] flex-wrap items-center justify-center gap-2">
                                                @can('editar clientes')
                                                    <a href="{{ route('cliente.edit', $cliente->id) }}"
                                                        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 whitespace-nowrap">Editar</a>
                                                @endcan
                                                @can('eliminar clientes')
                                                    <form action="{{ route('cliente.destroy', $cliente->id) }}" method="post"
                                                        class="inline-block"
                                                        onsubmit="return confirm('¿Eliminar este cliente?')">
                                                        @csrf @method('DELETE')<button type="submit"
                                                            class="inline-flex items-center justify-center rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 whitespace-nowrap">Eliminar</button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </x-card>
@endsection
