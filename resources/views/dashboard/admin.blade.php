@extends('layouts.app')

@section('title', 'Dashboard - Administrador')
@section('page-title', 'Panel de Control - Administrador')

@section('content')
    <div class="void-page-heading mb-7 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="void-page-eyebrow">VØID / Administración</p>
            <h2 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-neutral-50">Resumen de tu negocio</h2>
            <p class="mt-1 text-sm text-neutral-400">Supervisa las cifras principales y accede rápidamente a la gestión de tu
                tienda.</p>
        </div>
        <span class="inline-flex items-center gap-2 text-xs font-medium text-neutral-500"><span
                class="w-2 h-2 rounded-full bg-emerald-400 shadow-sm shadow-emerald-400/50"></span>Panel operativo</span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5">
        <x-stat-card label="Total de productos" :value="number_format($totalProductos)" icon="box" />
        <x-stat-card label="Total de empleados" :value="number_format($totalEmpleados)" icon="users" />
        <x-stat-card label="Ventas del mes" :value="number_format($ventasMes)" icon="chart" />
        <x-stat-card label="Ingresos del mes" :value="'$' . number_format($ingresosMes, 2)" icon="tag" />
    </div>
    <div class="mt-7 mb-4">
        <h3 class="text-base font-semibold text-neutral-100">Accesos de gestión</h3>
        <p class="mt-1 text-sm text-neutral-500">Las herramientas principales de tu empresa.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">
        <x-card title="Productos" subtitle="Gestión de inventario de la tienda">
            <p class="text-sm text-neutral-400 mb-5">Administra los productos, precios y disponibilidad de stock en tu
                tienda.</p>
            <x-button variant="primary" href="{{ route('productos.index') }}">Ver productos</x-button>
        </x-card>
        <x-card title="Facturación" subtitle="Historial y emisión de facturas">
            <p class="text-sm text-neutral-400 mb-5">Revisa las facturas registradas en el mes y emite nuevas ventas
                rápidamente.</p>
            <x-button variant="secondary" href="{{ route('facturas.index') }}">Ver facturas</x-button>
        </x-card>
        <x-card title="Categorías" subtitle="Organización del catálogo">
            <p class="text-sm text-neutral-400 mb-5">Clasifica y organiza los productos para facilitar la búsqueda en el
                catálogo.</p>
            <x-button variant="secondary" href="{{ route('categoria.index') }}">Ver categorías</x-button>
        </x-card>
    </div>
    <div class="mt-5">
        <x-card title="Reportes y movimientos de inventario" subtitle="Análisis de stock y rendimiento de ventas">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="max-w-2xl text-sm text-neutral-400">Consulta alertas de stock bajo, productos agotados, historial
                    de movimientos y rendimiento por empleado de tu tienda.</p>
                <x-button variant="primary" href="{{ route('reportes.index') }}">Abrir reportes <span
                        aria-hidden="true">→</span></x-button>
            </div>
        </x-card>
    </div>
@endsection
