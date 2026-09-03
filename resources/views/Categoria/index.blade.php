@extends('layouts.app')

@section('title', 'Categorías')

@section('content')
    <x-card>
        <div class="container mx-auto mt-10">

        <div class="bg-white shadow-lg rounded-lg p-6">

            <div class="flex justify-between items-center mb-6">

                <h2 class="text-3xl font-bold text-gray-700">
                    Listado de Categorías
                </h2>

                <a href="{{ route('categoria.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">

                    Nueva Categoría

                </a>

            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">

                    {{ session('success') }}
                </div>
            @endif

             @if(session('actualizar'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">

                    {{ session('actualizar') }}
                </div>
            @endif

            @if(session('eliminar'))
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
                            Nombre Categoría
                        </th>

                        <th class="border px-4 py-2">
                            Descripción
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

                @foreach ($categorias as $categoria)
                    <tr class="text-center hover:bg-gray-50">
                        <td class="border px-4 py-2">{{ $categoria->id}}</td>
                        <td class="border px-4 py-2">{{ $categoria->nombre_categoria}}</td>
                        <td class="border px-4 py-2">{{ $categoria->descripcion}}</td>
                        <td class="border px-4 py-2">
                            @if ($categoria->estado)
                                <span class="bg-green-200 text-green-800 px-2 py-1 rounded-full text-xs">Activo</span>
                            @else
                                <span class="bg-red-200 text-red-800 px-2 py-1 rounded-full text-xs">Inactivo</span>
                            @endif
                        </td>
                        <td class="border px-4 py-2">
                            <a href="{{ route('categoria.edit', $categoria->id)}}"class="bg-blue-400 hover:bg-blue-600 text-white rounded px-2 py-2">Editar</a>
                            <form action="{{ route('categoria.destroy',$categoria->id)}}" method="post">
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
 

