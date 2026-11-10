@extends('layouts.app')

@section('title', 'Dashboard - Empleado')
@section('page-title', 'Panel de Control - Empleado')

@section('content')

    {{-- Tarjetas estadísticas para el empleado --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
        <x-stat-card
            label="Ventas realizadas hoy"
            :value="number_format($ventasHoy)"
            icon="chart"
        />
        <x-stat-card
            label="Ingresos recaudados hoy"
            :value="'$' . number_format($ingresosHoy, 2)"
            icon="tag"
        />
        <x-stat-card
            label="Productos disponibles"
            :value="number_format($productosTienda)"
            icon="box"
        />
    </div>

    {{-- Últimas facturas generadas por el empleado --}}
    <div class="mt-6">
        <x-card title="Facturas recientes" subtitle="Tus últimas 5 ventas registradas" :padding="false">
            <x-slot:actions>
                <x-button variant="primary" href="{{ route('facturas.create') }}">Nueva venta</x-button>
            </x-slot:actions>

            <x-table :headers="['Factura #', 'Cliente', 'Estado', 'Total', 'Fecha']">
                @forelse ($facturasRecientes as $factura)
                    @php
                        $nombreCliente = trim(($factura->cliente?->nombre ?? '').' '.($factura->cliente?->apellido ?? ''));
                    @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3.5 font-medium text-slate-800">
                            #{{ $factura->id }}
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">
                            {{ $nombreCliente ?: 'Cliente no asignado' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <x-badge :status="$factura->estado" />
                        </td>
                        <td class="px-5 py-3.5 font-semibold text-slate-800">
                            ${{ number_format((float) $factura->total, 2) }}
                        </td>
                        <td class="px-5 py-3.5 text-slate-500 whitespace-nowrap">
                            {{ $factura->fecha?->format('d/m/Y') ?? 'Sin fecha' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400">
                            No has registrado facturas el día de hoy.
                        </td>
                    </tr>
                @endforelse
            </x-table>
        </x-card>
    </div>

@endsection
