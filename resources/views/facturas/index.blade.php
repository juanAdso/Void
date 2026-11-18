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

            <div class="w-full max-w-full overflow-hidden rounded-xl border border-slate-800">
                <table class="datatable min-w-full w-full border-collapse">
                    <thead>
                        <tr>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Subtotal</th>
                            <th>IVA</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th data-searchable="false" data-orderable="false">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($facturas as $factura)
                            <tr>
                                <td>{{ $factura->id }}</td>

                                <td>
                                    {{ $factura->cliente?->nombre ?? 'Cliente no disponible' }}
                                    {{ $factura->cliente?->apellido ?? '' }}
                                </td>

                                <td>
                                    {{ $factura->fecha?->format('d/m/Y') ?? 'Sin fecha' }}
                                </td>

                                <td>
                                    {{ number_format((float) $factura->subtotal, 2, ',', '.') }}
                                </td>

                                <td>
                                    {{ number_format((float) $factura->iva, 2, ',', '.') }}
                                </td>

                                <td class="text-right font-semibold">
                                    {{ number_format((float) $factura->total, 2, ',', '.') }}
                                </td>

                                <td>
                                    @if ($factura->estado === 'pagada')
                                        <span
                                            class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-800">
                                            Pagada
                                        </span>
                                    @elseif ($factura->estado === 'pendiente')
                                        <span
                                            class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">
                                            Pendiente
                                        </span>
                                    @else
                                        <span
                                            class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800">
                                            Anulada
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div class="flex min-w-[190px] flex-wrap items-center justify-center gap-2">
                                        <a href="{{ route('facturas.show', $factura->id) }}"
                                            class="inline-flex items-center justify-center rounded-lg border border-blue-500/40 bg-blue-500/10 px-3 py-2 text-xs font-semibold text-blue-300 transition hover:bg-blue-500/20">
                                            @if ($factura->estado !== 'anulada' && auth()->user()->tienePermisoActivo('editar facturas'))
                                                Editar factura
                                            @else
                                                Ver factura
                                            @endif
                                        </a>

                                        @can('eliminar facturas')
                                            @if ($factura->estado === 'pendiente')
                                                <form action="{{ route('facturas.destroy', $factura->id) }}" method="POST"
                                                    onsubmit="return confirm('¿Eliminar la factura #{{ $factura->id }}? También se eliminarán sus productos asociados, se devolverán las unidades al inventario y se registrarán los movimientos.');">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                        class="inline-flex items-center justify-center rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-red-700">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-neutral-500">
                                                    No eliminable: {{ $factura->estado }}
                                                </span>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-10 text-center text-sm text-slate-400">
                                    Todavía no hay facturas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </x-card>
@endsection
