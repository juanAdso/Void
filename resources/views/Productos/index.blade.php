@extends('layouts.app')

@section('title', 'Productos')

@section('content')
    <x-card>

        <div class="container mx-auto mt-10">

            <div class="bg-white shadow-lg rounded-lg p-6">

                <div class="flex justify-between items-center mb-6">

                    <h2 class="text-3xl font-bold text-gray-700">
                        Listado de Productos
                    </h2>

                    <a href="{{ route('productos.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">

                        Nuevo Producto

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
                                Descripción
                            </th>
                            <th class="border px-4 py-2">
                                Precio
                            </th>
                            <th class="border px-4 py-2">
                                Stock
                            </th>
                            <th class="border px-4 py-2">
                                Imagen
                            </th>
                            <th class="border px-4 py-2">
                                Estado
                            </th>
                            <th class="border px-4 py-2">
                                Categoría
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach ($productos as $producto)
                            <tr class="text-center hover:bg-gray-50">
                                <td class="border px-4 py-2">{{ $producto->id }}</td>
                                <td class="border px-4 py-2">{{ $producto->descripcion }}</td>
                                <td class="border px-4 py-2">{{ $producto->precio }}</td>
                                <td class="border px-4 py-2">{{ $producto->stock }}</td>
                                <td class="border px-4 py-2">{{ $producto->imagen }}</td>
                                </td>
                                <td class="border px-4 py-2">{{ $producto->categoria->nombre_categoria}}</td>
                                <td class="border px-4 py-2">
                                    @if ($producto->estado)
                                        <span class="bg-green-500 text-white px-2 py-1 rounded">Activo</span>
                                    @else
                                        <span class="bg-red-500 text-white px-2 py-1 rounded">Inactivo</span>
                                    @endif
                                </td>
                                <td class="border px-4 py-2 flex justify-center gap-2">
                                    <a
                                        href="{{ route('productos.edit', $producto->id) }}"class="bg-blue-400 hover:bg-blue-600 text-white rounded px-2 py-2">Editar</a>
                                    <form action="{{ route('productos.destroy', $producto->id) }}" method="post">
                                
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
