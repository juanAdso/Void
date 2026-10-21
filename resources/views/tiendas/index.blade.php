@extends('layouts.app')

@section('title', 'Tiendas')

@section('content')
    <div class="container mx-auto max-w-7xl">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Tiendas</h1>
                <p class="mt-1 text-sm text-slate-500">Administra las tiendas disponibles en VOID.</p>
            </div>
            <a href="{{ route('tiendas.create') }}" class="inline-flex items-center justify-center rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700">
                Registrar tienda
            </a>
        </div>

        @if (session('success'))
            <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
        @endif
        @if (session('actualizar'))
            <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('actualizar') }}</div>
        @endif
        @if (session('eliminar'))
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('eliminar') }}</div>
        @endif

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            @if ($tiendas->isEmpty())
                <div class="px-6 py-12 text-center">
                    <h2 class="text-lg font-semibold text-slate-700">No hay tiendas registradas</h2>
                    <p class="mt-1 text-sm text-slate-500">Registra la primera tienda para comenzar a organizar sus catálogos.</p>
                    <a href="{{ route('tiendas.create') }}" class="mt-5 inline-flex rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700">Crear tienda</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">ID</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Tienda</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Contacto</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($tiendas as $tienda)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-500">{{ $tienda->id }}</td>
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-slate-800">{{ $tienda->nombre }}</div>
                                        <div class="mt-1 max-w-md truncate text-sm text-slate-500">{{ $tienda->descripcion ?: 'Sin descripción' }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-slate-600">
                                        <div>{{ $tienda->correo ?: 'Sin correo' }}</div>
                                        <div class="mt-1 text-slate-400">{{ $tienda->telefono ?: 'Sin teléfono' }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        @if ($tienda->estado)
                                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-800">Activa</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-800">Inactiva</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right text-sm">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('tiendas.show', $tienda->id) }}" class="rounded-md border border-slate-200 px-3 py-1.5 font-medium text-slate-700 hover:bg-slate-100">Ver</a>
                                            <a href="{{ route('tiendas.edit', $tienda->id) }}" class="rounded-md bg-primary-600 px-3 py-1.5 font-medium text-white hover:bg-primary-700">Editar</a>
                                            <form action="{{ route('tiendas.destroy', $tienda->id) }}" method="POST" onsubmit="return confirm('¿Deseas eliminar esta tienda?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-md border border-red-200 px-3 py-1.5 font-medium text-red-700 hover:bg-red-50">Eliminar</button>
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
