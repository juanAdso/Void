@extends('layouts.app')
@section('page-title', 'Gestión de facturas')
@section('title', 'Facturas')
@section('content')
    <x-card>
        <div class="container mx-auto mt-2">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Listado de facturas</h2>
                    <p class="mt-1 text-sm text-neutral-400">Consulta cada factura para revisar sus productos y operaciones
                        disponibles.</p>
                </div>
                @can('crear facturas')
                    <a href="{{ route('facturas.create') }}"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">Nueva
                        factura</a>
                @endcan
            </div>

            <div class="w-full max-w-full overflow-x-auto rounded-lg border border-slate-200">
                <table class="min-w-[1050px] w-full border-collapse">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border px-4 py-3 text-left">Factura</th>
                            <th class="border px-4 py-3 text-left">Cliente</th>
                            <th class="border px-4 py-3">Fecha</th>
                            <th class="border px-4 py-3 text-right">Subtotal</th>
                            <th class="border px-4 py-3 text-right">IVA</th>
                            <th class="border px-4 py-3 text-right">Total</th>
                            <th class="border px-4 py-3">Estado</th>
                            <th class="border px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($facturas as $factura)
                            <tr class="text-center hover:bg-gray-50">
                                <td class="border px-4 py-3 font-semibold">#{{ $factura->id }}</td>
                                <td class="border px-4 py-3 text-left">
                                    {{ $factura->cliente?->nombre ?? 'Cliente no disponible' }}
                                    {{ $factura->cliente?->apellido ?? '' }}
                                </td>
                                <td class="border px-4 py-3">{{ $factura->fecha?->format('d/m/Y') ?? 'Sin fecha' }}</td>
                                <td class="border px-4 py-3 text-right">
                                    {{ number_format((float) $factura->subtotal, 2, ',', '.') }}</td>
                                <td class="border px-4 py-3 text-right">
                                    {{ number_format((float) $factura->iva, 2, ',', '.') }}</td>
                                <td class="border px-4 py-3 text-right font-semibold">
                                    {{ number_format((float) $factura->total, 2, ',', '.') }}</td>
                                <td class="border px-4 py-3">
                                    @if ($factura->estado === 'pagada')
                                        <span
                                            class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-800">Pagada</span>
                                    @elseif ($factura->estado === 'pendiente')
                                        <span
                                            class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">Pendiente</span>
                                    @else
                                        <span
                                            class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800">Anulada</span>
                                    @endif
                                </td>
                                <td class="border px-4 py-3">
                                    <div class="flex min-w-[190px] flex-wrap items-center justify-center gap-2">
                                        <a href="{{ route('facturas.show', $factura->id) }}"
                                            class="inline-flex items-center justify-center rounded-lg border border-blue-500/40 bg-blue-500/10 px-3 py-2 text-xs font-semibold text-blue-300 transition hover:bg-blue-500/20">Abrir
                                            factura</a>

                                        @can('eliminar facturas')
                                            @if ($factura->estado === 'pendiente')
                                                <form action="{{ route('facturas.destroy', $factura->id) }}" method="POST"
                                                    onsubmit="return confirm('¿Eliminar la factura #{{ $factura->id }}? También se eliminarán sus productos asociados, se devolverán las unidades al inventario y se registrarán los movimientos.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center justify-center rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-red-700">Eliminar</button>
                                                </form>
                                            @else
                                                <span class="text-xs text-neutral-500">No eliminable:
                                                    {{ $factura->estado }}</span>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center text-sm text-neutral-400">Todavía no hay
                                    facturas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-card>
@endsection
