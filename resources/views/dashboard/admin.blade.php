@extends('layouts.app')

@section('title', 'Dashboard - Administrador')
@section('page-title', 'Panel de Control - Administrador')

@section('content')

    {{-- Tarjetas estadísticas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6">
        <x-stat-card
            label="Total de productos"
            :value="number_format($totalProductos)"
            icon="box"
        />
        <x-stat-card
            label="Total de empleados"
            :value="number_format($totalEmpleados)"
            icon="users"
        />
        <x-stat-card
            label="Ventas del mes"
            :value="number_format($ventasMes)"
            icon="chart"
        />
        <x-stat-card
            label="Ingresos del mes"
            :value="'$' . number_format($ingresosMes, 2)"
            icon="tag"
        />
    </div>

    {{-- Accesos directos y gestión rápida --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
        <x-card title="Productos" subtitle="Gestión de inventario de la tienda">
            <p class="text-sm text-slate-500 mb-4">Administra los productos, precios y disponibilidad de stock en tu tienda.</p>
            <x-button variant="primary" href="{{ route('productos.index') }}">Ver productos</x-button>
        </x-card>

        <x-card title="Facturación" subtitle="Historial y emisión de facturas">
            <p class="text-sm text-slate-500 mb-4">Revisa las facturas registradas en el mes y emite nuevas ventas rápidamente.</p>
            <x-button variant="secondary" href="{{ route('facturas.index') }}">Ver facturas</x-button>
        </x-card>

        <x-card title="Categorías" subtitle="Organización del catálogo">
            <p class="text-sm text-slate-500 mb-4">Clasifica y organiza los productos para facilitar la búsqueda en el catálogo.</p>
            <x-button variant="secondary" href="{{ route('categoria.index') }}">Ver categorías</x-button>
        </x-card>
    </div>

@endsection
