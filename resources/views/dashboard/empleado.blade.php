@extends('layouts.app')

@section('title', 'Dashboard - Empleado')
@section('page-title', 'Panel de Control - Empleado')

@section('content')
    <div class="void-page-heading mb-7">
        <p class="void-page-eyebrow">VØID / Operaciones</p>
        <h2 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-neutral-50">Tu jornada de trabajo</h2>
        <p class="mt-1 text-sm text-neutral-400">Consulta tu actividad de hoy y mantén a la mano tus últimas ventas.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5">
        <x-stat-card label="Ventas realizadas hoy" :value="number_format($ventasHoy)" icon="chart" />
        <x-stat-card label="Ingresos recaudados hoy" :value="'$' . number_format($ingresosHoy, 2)" icon="tag" />
        <x-stat-card label="Productos disponibles" :value="number_format($productosTienda)" icon="box" />
    </div>
    <div class="mt-7">
        <x-card title="Facturas recientes" subtitle="Tus últimas 5 ventas registradas" :padding="false">
            <x-slot:actions><x-button variant="primary" href="{{ route('facturas.create') }}">Nueva venta <span aria-hidden="true">+</span></x-button></x-slot:actions>
            <x-table :headers="['Factura #', 'Cliente', 'Estado', 'Total', 'Fecha']">
                @forelse ($facturasRecientes as $factura)
                    @php $nombreCliente = trim(($factura->cliente?->nombre ?? '').' '.($factura->cliente?->apellido ?? '')); @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3.5 font-semibold text-neutral-100">#{{ $factura->id }}</td>
                        <td class="px-5 py-3.5 text-neutral-300">{{ $nombreCliente ?: 'Cliente no asignado' }}</td>
                        <td class="px-5 py-3.5"><x-badge :status="$factura->estado" /></td>
                        <td class="px-5 py-3.5 font-semibold text-neutral-100">&#36;{{ number_format((float) $factura->total, 2) }}</td>
                        <td class="px-5 py-3.5 text-neutral-400 whitespace-nowrap">{{ $factura->fecha?->format('d/m/Y') ?? 'Sin fecha' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-sm text-neutral-500">No has registrado facturas el día de hoy.</td></tr>
                @endforelse
            </x-table>
        </x-card>
    </div>
@endsection