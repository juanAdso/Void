@extends('layouts.app')
@section('page-title', 'Detalles de facturación')
@section('title', 'Detalle Facturas')
@section('content')
    <div class="mx-auto w-full max-w-full min-w-0">
        <div class="w-full min-w-0 rounded-xl bg-white p-4 shadow-lg sm:p-6">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-3xl font-bold text-neutral-950">Detalle de Facturas</h2>
                @can('crear detallefacturas')
                    <a href="{{ route('detalle_facturas.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Nuevo Detalle de Factura</a>
                @endcan
            </div>
            @if (session('success'))
                <div class="mb-4 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-800">
                    {{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded border border-red-400 bg-red-100 px-4 py-3 text-red-800">{{ session('error') }}
                </div>
            @endif
            <div class="w-full max-w-full overflow-x-auto rounded-lg border border-slate-200">
                <table class="min-w-[900px] w-full border-collapse">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="border px-4 py-2">ID</th>
                            <th class="border px-4 py-2">Factura</th>
                            <th class="border px-4 py-2">Producto</th>
                            <th class="border px-4 py-2">Cantidad</th>
                            <th class="border px-4 py-2">Precio Unitario</th>
                            <th class="border px-4 py-2">Subtotal</th>
                            <th class="border px-4 py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detalle_facturas as $detalle_factura)
                            <tr class="text-center hover:bg-blue-50">
                                <td class="border px-4 py-2">{{ $detalle_factura->id }}</td>
                                <td class="border px-4 py-2">{{ $detalle_factura->factura?->id }}</td>
                                <td class="border px-4 py-2">{{ $detalle_factura->producto?->nombre }}</td>
                                <td class="border px-4 py-2">{{ $detalle_factura->cantidad }}</td>
                                <td class="border px-4 py-2">{{ $detalle_factura->precio_unitario }}</td>
                                <td class="border px-4 py-2">{{ $detalle_factura->subtotal }}</td>
                                <td class="border px-4 py-2">
                                    <div class="flex min-w-[180px] flex-wrap items-center justify-center gap-2">
                                        @if ($detalle_factura->factura?->estado === 'pendiente')
                                            @can('editar detallefacturas')
                                                <a href="{{ route('detalle_facturas.edit', $detalle_factura->id) }}"
                                                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 whitespace-nowrap">Editar</a>
                                            @endcan
                                            @can('eliminar detallefacturas')
                                                <form action="{{ route('detalle_facturas.destroy', $detalle_factura->id) }}"
                                                    method="post" class="inline-block"
                                                    onsubmit="return confirm('¿Eliminar este detalle de factura?')">@csrf
                                                    @method('DELETE')<button type="submit"
                                                        class="inline-flex items-center justify-center rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 whitespace-nowrap">Eliminar</button>
                                                </form>
                                            @endcan
                                        @else
                                            <span class="rounded-lg bg-neutral-800 px-3 py-2 text-xs font-semibold text-neutral-300">
                                                Factura {{ $detalle_factura->factura?->estado ?? 'no disponible' }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
