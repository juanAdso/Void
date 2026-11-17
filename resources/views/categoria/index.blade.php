@extends('layouts.app')
@section('page-title', 'Gestión de categorías')
@section('title', 'Categorías')
@section('content')
    @php(
    $mostrarAcciones = auth()->user()->canAny(['editar categorias', 'eliminar categorias'])
)
    <x-card>
        <div class="container mx-auto mt-10">
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <h2 class="text-2xl font-bold text-gray-800">Listado de Categorías</h2>
                    @can('crear categorias')
                        <a href="{{ route('categoria.create') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Nueva Categoría</a>
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
                    <table class="min-w-[760px] w-full border-collapse">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border px-4 py-2">ID</th>
                                <th class="border px-4 py-2">Nombre Categoría</th>
                                <th class="border px-4 py-2">Descripción</th>
                                <th class="border px-4 py-2">Estado</th>
                                @if ($mostrarAcciones)
                                    <th class="border px-4 py-2">Acciones</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categorias as $categoria)
                                <tr class="text-center hover:bg-gray-50">
                                    <td class="border px-4 py-2">{{ $categoria->id }}</td>
                                    <td class="border px-4 py-2">{{ $categoria->nombre }}</td>
                                    <td class="border px-4 py-2">{{ $categoria->descripcion }}</td>
                                    <td class="border px-4 py-2">
                                        @if ($categoria->estado)
                                            <span
                                            class="bg-green-200 text-green-800 px-2 py-1 rounded-full text-xs">Activo</span>@else<span
                                                class="bg-red-200 text-red-800 px-2 py-1 rounded-full text-xs">Inactivo</span>
                                        @endif
                                    </td>
                                    @if ($mostrarAcciones)
                                        <td class="border px-4 py-2">
                                            <div class="flex min-w-[180px] flex-wrap items-center justify-center gap-2">
                                                @can('editar categorias')
                                                    <a href="{{ route('categoria.edit', $categoria->id) }}"
                                                        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 whitespace-nowrap">Editar</a>
                                                @endcan
                                                @can('eliminar categorias')
                                                    <form action="{{ route('categoria.destroy', $categoria->id) }}"
                                                        method="post" onsubmit="return confirm('¿Eliminar esta categoría?')">
                                                        @csrf
                                                        @method('DELETE')<button type="submit"
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
