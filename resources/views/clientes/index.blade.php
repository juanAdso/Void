@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
    <x-card>

        <div class="container mx-auto mt-10">

            <div class="bg-white shadow-lg rounded-lg p-6">

                <div class="flex justify-between items-center mb-6">

                    <h2 class="text-3xl font-bold text-gray-700">
                        Listado de Clientes
                    </h2>

                    <a href="{{ route('cliente.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">

                        Nuevo Cliente

                    </a>

                </div>

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
                                Nombre
                            </th>

                            <th class="border px-4 py-2">
                                Apellido
                            </th>
                            <th class="border px-4 py-2">
                                Documento
                            </th>
                            <th class="border px-4 py-2">
                                Telefono
                            </th>
                            <th class="border px-4 py-2">
                                Correo
                            </th>
                            <th class="border px-4 py-2">
                                Dirección
                            </th>

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
                                <td class="border px-4 py-2">
                                    <a
                                        href="{{ route('cliente.edit', $cliente->id) }}"class="bg-blue-400 hover:bg-blue-600 text-white rounded px-2 py-2">Editar</a>
                                    <form action="{{ route('cliente.destroy', $cliente->id) }}" method="post">
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
