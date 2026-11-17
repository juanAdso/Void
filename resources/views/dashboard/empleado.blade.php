@extends('layouts.app')

@section('title', 'Dashboard - Empleado')
@section('page-title', 'Panel de Control - Empleado')

@section('content')
    <div class="void-page-heading mb-7">
        <p class="void-page-eyebrow">VØID / Operaciones</p>
        <h2 class="mt-2 text-2xl font-bold tracking-tight text-neutral-50 sm:text-3xl">Tu jornada de trabajo</h2>
        <p class="mt-1 text-sm text-neutral-400">Consulta tu actividad de hoy y mantén a la mano tus últimas ventas.</p>
    </div>

    @if (auth()->user()->canAny(['ver facturas', 'ver productos']))
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 sm:gap-5">
            @can('ver facturas')
                <x-stat-card label="Ventas realizadas hoy" :value="number_format($ventasHoy)" icon="chart" />
                <x-stat-card label="Ingresos recaudados hoy" :value="'$' . number_format($ingresosHoy, 2)" icon="tag" />
            @endcan
            @can('ver productos')
                <x-stat-card label="Productos disponibles" :value="number_format($productosTienda)" icon="box" />
            @endcan
        </div>
    @endif

    @if (auth()->user()->canAny(['ver facturas', 'crear facturas']))
        <div class="mt-7">
            <x-card title="Facturas recientes" subtitle="Tus últimas 5 ventas registradas" :padding="false">
                <x-slot:actions>
                    @can('crear facturas')
                        <x-button variant="primary" href="{{ route('facturas.create') }}">Nueva venta <span aria-hidden="true">+</span></x-button>
                    @endcan
                </x-slot:actions>

                @can('ver facturas')
                    <x-table :headers="['Factura #', 'Cliente', 'Estado', 'Total', 'Fecha', 'Acciones']">
                        @forelse ($facturasRecientes as $factura)
                            @php
                                $nombreCliente = trim(($factura->cliente?->nombre ?? '') . ' ' . ($factura->cliente?->apellido ?? ''));
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3.5 font-semibold text-neutral-100">#{{ $factura->id }}</td>
                                <td class="px-5 py-3.5 text-neutral-300">{{ $nombreCliente ?: 'Cliente no asignado' }}</td>
                                <td class="px-5 py-3.5"><x-badge :status="$factura->estado" /></td>
                                <td class="px-5 py-3.5 font-semibold text-neutral-100">&#36;{{ number_format((float) $factura->total, 2) }}</td>
                                <td class="whitespace-nowrap px-5 py-3.5 text-neutral-400">{{ $factura->fecha?->format('d/m/Y') ?? 'Sin fecha' }}</td>
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('facturas.show', $factura->id) }}"
                                        class="inline-flex items-center rounded-lg border border-blue-500/30 bg-blue-500/10 px-3 py-2 text-xs font-semibold text-blue-300 transition hover:bg-blue-500/20">Consultar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-sm text-neutral-500">No has registrado facturas el día de hoy.</td>
                            </tr>
                        @endforelse
                    </x-table>
                @else
                    <p class="px-5 py-8 text-sm text-neutral-400">Puedes registrar una venta nueva desde este panel.</p>
                @endcan
            </x-card>
        </div>
    @endif
@endsection
