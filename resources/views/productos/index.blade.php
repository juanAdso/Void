@extends('layouts.app')
@section('page-title', 'Gestión de productos')
@section('title', 'Productos')
@section('content')
    @php
        $mostrarAcciones = auth()
            ->user()
            ->canAny(['editar productos', 'eliminar productos']);
    @endphp
    <div class="mx-auto w-full max-w-full min-w-0">
        <div class="w-full min-w-0 rounded-xl bg-white p-4 shadow-lg sm:p-6">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-3xl font-bold text-gray-700">Listado de Productos</h2>
                @can('crear productos')
                    <a href="{{ route('productos.create') }}"
                        class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Nuevo Producto</a>
                @endcan
            </div>
            @foreach (['success' => 'green', 'error' => 'red'] as $tipo => $color)
                @if (session($tipo))
                    <div
                        class="mb-4 rounded border border-{{ $color }}-400 bg-{{ $color }}-100 px-4 py-3 text-{{ $color }}-700">
                        {{ session($tipo) }}</div>
                @endif
            @endforeach
            @if ($productos->isEmpty())
                <p class="py-8 text-center text-gray-500">No hay productos registrados.</p>
            @else
                <div class="w-full max-w-full overflow-x-auto rounded-lg border border-slate-200">
                    <table class="datatable min-w-full w-full border-collapse">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border px-4 py-2">ID</th>
                                <th class="border px-4 py-2" data-searchable="false" data-orderable="false">Imagen</th>
                                <th class="border px-4 py-2">Nombre</th>
                                <th class="border px-4 py-2">Descripción</th>
                                <th class="border px-4 py-2">Precio</th>
                                <th class="border px-4 py-2">Stock</th>
                                <th class="border px-4 py-2">Categoría</th>
                                <th class="border px-4 py-2">Estado</th>
                                @if ($mostrarAcciones)
                                    <th class="border px-4 py-2" data-searchable="false" data-orderable="false">Acciones
                                    </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productos as $producto)
                                <tr class="text-center hover:bg-gray-50">
                                    <td class="border px-4 py-2">{{ $producto->id }}</td>
                                    <td class="border px-4 py-2">
                                        <div class="flex justify-center">
                                            @if ($producto->imagen_url)
                                                <img src="{{ $producto->imagen_url }}"
                                                    alt="Imagen de {{ $producto->nombre }}" loading="lazy"
                                                class="h-14 w-14 rounded-lg border border-slate-200 object-cover">@else<span
                                                    class="flex h-14 w-14 items-center justify-center rounded-lg bg-slate-100 text-xs font-semibold text-slate-400">Sin
                                                    imagen</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="border px-4 py-2">{{ $producto->nombre }}</td>
                                    <td class="border px-4 py-2">{{ $producto->descripcion }}</td>
                                    <td class="border px-4 py-2">{{ number_format($producto->precio, 2, ',', '.') }}</td>
                                    <td class="border px-4 py-2">{{ $producto->stock }}</td>
                                    <td class="border px-4 py-2">{{ $producto->categoria?->nombre ?? 'Sin categoría' }}
                                    </td>
                                    <td class="border px-4 py-2">
                                        @if ($producto->estado)
                                        <span class="rounded bg-green-500 px-2 py-1 text-white">Activo</span>@else<span
                                                class="rounded bg-red-500 px-2 py-1 text-white">Inactivo</span>
                                        @endif
                                    </td>
                                    @if ($mostrarAcciones)
                                        <td class="border px-4 py-2">
                                            <div class="flex justify-center gap-2">
                                                @can('editar productos')
                                                    <a href="{{ route('productos.edit', $producto->id) }}"
                                                        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 whitespace-nowrap">Editar</a>
                                                @endcan
                                                @can('eliminar productos')
                                                    <form action="{{ route('productos.destroy', $producto->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('¿Eliminar el producto {{ addslashes($producto->nombre) }}? Si tiene facturas o movimientos asociados, no se eliminará y VOID te mostrará los registros relacionados.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="inline-flex items-center justify-center whitespace-nowrap rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">Eliminar</button>
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
            @endif
        </div>
    </div>
@endsection
