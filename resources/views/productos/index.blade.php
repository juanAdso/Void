@extends('layouts.app')

@section('title', 'Productos')

@section('content')
    <div class="container mx-auto mt-10">
        <div class="rounded-lg bg-white p-6 shadow-lg">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-3xl font-bold text-gray-700">Listado de Productos</h2>
                <a href="{{ route('productos.create') }}" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Nuevo Producto</a>
            </div>

            @foreach (['success' => 'green', 'error' => 'red'] as $tipo => $color)
                @if (session($tipo))
                    <div class="mb-4 rounded border border-{{ $color }}-400 bg-{{ $color }}-100 px-4 py-3 text-{{ $color }}-700">{{ session($tipo) }}</div>
                @endif
            @endforeach

            @if($productos->isEmpty())
                <p class="py-8 text-center text-gray-500">No hay productos registrados.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border px-4 py-2">ID</th>
                                <th class="border px-4 py-2">Nombre</th>
                                <th class="border px-4 py-2">Descripción</th>
                                <th class="border px-4 py-2">Precio</th>
                                <th class="border px-4 py-2">Stock</th>
                                <th class="border px-4 py-2">Categoría</th>
                                <th class="border px-4 py-2">Estado</th>
                                <th class="border px-4 py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productos as $producto)
                                <tr class="text-center hover:bg-gray-50">
                                    <td class="border px-4 py-2">{{ $producto->id_producto }}</td>
                                    <td class="border px-4 py-2">{{ $producto->nombre }}</td>
                                    <td class="border px-4 py-2">{{ $producto->descripcion }}</td>
                                    <td class="border px-4 py-2">{{ number_format($producto->precio, 2, ',', '.') }}</td>
                                    <td class="border px-4 py-2">{{ $producto->stock }}</td>
                                    <td class="border px-4 py-2">{{ $producto->categoria?->nombre ?? 'Sin categoría' }}</td>
                                    <td class="border px-4 py-2">
                                        @if ($producto->estado)
                                            <span class="rounded bg-green-500 px-2 py-1 text-white">Activo</span>
                                        @else
                                            <span class="rounded bg-red-500 px-2 py-1 text-white">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('productos.edit', $producto->id_producto) }}" class="rounded bg-blue-500 px-2 py-2 text-white hover:bg-blue-600">Editar</a>
                                            <form action="{{ route('productos.destroy', $producto->id_producto) }}" method="POST" onsubmit="return confirm('¿Eliminar este producto?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded bg-red-500 px-2 py-2 text-white hover:bg-red-600">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
