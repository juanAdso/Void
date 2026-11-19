@extends('layouts.app')
@section('page-title', 'Gestión de facturas')
@section('title', 'Facturas')
@section('content')
    @php
        $puedeCambiarEstado =
            auth()->user()->rol === 'administrador' && auth()->user()->tienePermisoActivo('editar facturas');
    @endphp

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

            <div class="w-full max-w-full overflow-x-auto rounded-xl border border-slate-800">
                <table class="datatable min-w-full w-full border-collapse">
                    <thead>
                        <tr>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th data-date-filter="true">Fecha</th>
                            <th>Subtotal</th>
                            <th>IVA</th>
                            <th>Total</th>
                            @if ($puedeCambiarEstado)
                                <th>Estado</th>
                            @endif
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

                                <td data-order="{{ $factura->fecha?->format('Y-m-d') ?? '' }}">
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

                                @if ($puedeCambiarEstado)
                                    <td>
                                        <div class="flex flex-wrap items-center gap-2">
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

                                            @if ($factura->estado === 'pendiente')
                                                <form action="{{ route('facturas.estado', $factura->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="estado" value="pagada">
                                                    <button type="submit" title="Marcar como pagada"
                                                        aria-label="Marcar factura #{{ $factura->id }} como pagada"
                                                        class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-emerald-500/40 bg-emerald-500/10 text-sm font-bold text-emerald-300 transition hover:bg-emerald-500/20">✓</button>
                                                </form>
                                            @endif

                                            @if (in_array($factura->estado, ['pendiente', 'pagada'], true))
                                                <form action="{{ route('facturas.estado', $factura->id) }}" method="POST"
                                                    onsubmit="return confirm('¿Anular la factura #{{ $factura->id }}? Se restituirán las unidades al inventario.');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="estado" value="anulada">
                                                    <button type="submit" title="Anular factura"
                                                        aria-label="Anular factura #{{ $factura->id }}"
                                                        class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-red-500/40 bg-red-500/10 text-sm font-bold text-red-300 transition hover:bg-red-500/20">✕</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                @endif

                                <td>
                                    <div class="flex min-w-[190px] flex-wrap items-center justify-center gap-2">
                                        <a href="{{ route('facturas.show', $factura->id) }}"
                                            class="inline-flex items-center justify-center rounded-lg border border-blue-500/40 bg-blue-500/10 px-3 py-2 text-xs font-semibold text-blue-300 transition hover:bg-blue-500/20">
                                            Ver factura
                                        </a>
                                        <a href="{{ route('facturas.imprimir', $factura->id) }}" target="_blank"
                                            rel="noopener" title="Imprimir factura #{{ $factura->id }}"
                                            aria-label="Imprimir factura #{{ $factura->id }}"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-neutral-700 text-neutral-300 transition hover:border-blue-500/50 hover:bg-blue-500/10 hover:text-blue-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="1.8"
                                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <polyline points="6 9 6 2 18 2 18 9" />
                                                <path d="M6 18H4v-8h16v8h-2" />
                                                <path d="M6 14h12v8H6z" />
                                            </svg>
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
                                                    No eliminable
                                                </span>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $puedeCambiarEstado ? 8 : 7 }}"
                                    class="px-5 py-10 text-center text-sm text-slate-400">
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
