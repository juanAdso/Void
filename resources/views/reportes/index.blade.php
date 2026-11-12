@extends('layouts.app')

@section('title', 'Inventario y Reportes')
@section('page-title', 'Inventario y Reportes')

@section('content')
    <div class="space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Inventario y Reportes de Tienda</h1>
                <p class="text-sm text-slate-500">Métricas comerciales, movimientos de stock y análisis de ventas en tiempo real.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Ventas de hoy" :value="number_format($ventasHoy)" icon="chart" />
            <x-stat-card label="Ingresos de hoy" :value="'$' . number_format($ingresosHoy, 2)" icon="tag" />
            <x-stat-card label="Ventas del mes" :value="number_format($ventasMes)" icon="chart" />
            <x-stat-card label="Ingresos del mes" :value="'$' . number_format($ingresosMes, 2)" icon="tag" />
        </div>

        <x-card title="Ingresos por Período Personalizado" subtitle="Consulta las ventas recaudadas en un rango de fechas">
            <form action="{{ route('reportes.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
                <div>
                    <label for="fecha_inicio" class="block text-xs font-semibold text-slate-700 uppercase mb-1">Fecha inicio</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ $fechaInicio }}"
                           class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white focus:border-primary-500">
                </div>
                <div>
                    <label for="fecha_fin" class="block text-xs font-semibold text-slate-700 uppercase mb-1">Fecha fin</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" value="{{ $fechaFin }}"
                           class="rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white focus:border-primary-500">
                </div>
                <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700">
                    Consultar Período
                </button>
            </form>

            <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap gap-6">
                <div>
                    <span class="text-xs text-slate-500 block">Facturas pagadas en el período:</span>
                    <span class="text-xl font-bold text-slate-800">{{ number_format($ventasPeriodo) }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Total ingresos recaudados:</span>
                    <span class="text-xl font-bold text-emerald-600">${{ number_format($ingresosPeriodo, 2) }}</span>
                </div>
            </div>
        </x-card>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-card title="Alertas de Stock Bajo (≤ 5 unidades)" subtitle="Productos con inventario crítico que requieren reabastecimiento" :padding="false">
                <x-table :headers="['Producto', 'Categoría', 'Stock']">
                    @forelse($productosStockBajo as $prod)
                        <tr class="hover:bg-amber-50/50">
                            <td class="px-5 py-3 font-medium text-slate-800 text-sm">
                                {{ $prod->nombre }}
                            </td>
                            <td class="px-5 py-3 text-slate-500 text-sm">
                                {{ $prod->categoria->nombre ?? 'General' }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="rounded-full bg-amber-100 text-amber-800 px-2.5 py-0.5 text-xs font-bold">
                                    {{ $prod->stock }} restante(s)
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-8 text-center text-sm text-slate-400">
                                No hay productos con stock bajo en este momento.
                            </td>
                        </tr>
                    @endforelse
                </x-table>
            </x-card>

            <x-card title="Productos Agotados" subtitle="Artículos sin existencias en tu tienda" :padding="false">
                <x-table :headers="['Producto', 'Categoría', 'Precio', 'Estado']">
                    @forelse($productosAgotados as $agotado)
                        <tr class="hover:bg-rose-50/50">
                            <td class="px-5 py-3 font-medium text-slate-800 text-sm">
                                {{ $agotado->nombre }}
                            </td>
                            <td class="px-5 py-3 text-slate-500 text-sm">
                                {{ $agotado->categoria->nombre ?? 'General' }}
                            </td>
                            <td class="px-5 py-3 text-slate-700 text-sm font-semibold">
                                ${{ number_format($agotado->precio, 2) }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="rounded-full bg-rose-100 text-rose-800 px-2.5 py-0.5 text-xs font-bold">
                                    Sin existencias
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-sm text-slate-400">
                                Excelente: no tienes productos agotados en tu tienda.
                            </td>
                        </tr>
                    @endforelse
                </x-table>
            </x-card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-card title="Top 10 Productos Más Vendidos" subtitle="Mayor rotación en ventas de la tienda" :padding="false">
                <x-table :headers="['Producto', 'Unidades Vendidas', 'Ingresos Generados']">
                    @forelse($productosMasVendidos as $top)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 text-sm font-medium text-slate-800">
                                {{ $top->nombre }}
                            </td>
                            <td class="px-5 py-3 text-sm font-bold text-primary-600">
                                {{ number_format($top->total_vendido) }} und.
                            </td>
                            <td class="px-5 py-3 text-sm font-semibold text-slate-700">
                                ${{ number_format((float) $top->total_ingresos, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-8 text-center text-sm text-slate-400">
                                Aún no se registran ventas facturadas en esta tienda.
                            </td>
                        </tr>
                    @endforelse
                </x-table>
            </x-card>

            <x-card title="Rendimiento por Empleado" subtitle="Ventas registradas y recaudadas por colaborador" :padding="false">
                <x-table :headers="['Empleado', 'Facturas Pagadas', 'Ingresos Totales']">
                    @forelse($ventasPorEmpleado as $colaborador)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 text-sm font-medium text-slate-800">
                                {{ $colaborador->nombre }} {{ $colaborador->apellido }}
                            </td>
                            <td class="px-5 py-3 text-sm text-slate-600">
                                {{ number_format($colaborador->total_facturas_pagadas ?? 0) }} ventas
                            </td>
                            <td class="px-5 py-3 text-sm font-bold text-emerald-600">
                                ${{ number_format((float) ($colaborador->total_ingresos_generados ?? 0), 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-8 text-center text-sm text-slate-400">
                                No hay empleados registrados con ventas en esta tienda.
                            </td>
                        </tr>
                    @endforelse
                </x-table>
            </x-card>
        </div>

        <x-card title="Historial de Movimientos de Inventario" subtitle="Auditoría de ingresos, salidas, ventas y devoluciones" :padding="false">
            <x-table :headers="['Fecha', 'Producto', 'Tipo de Movimiento', 'Cantidad', 'Responsable', 'Motivo']">
                @forelse($movimientos as $mov)
                    <tr class="hover:bg-slate-50 text-sm">
                        <td class="px-5 py-3 text-slate-500 whitespace-nowrap">
                            {{ $mov->fecha ? \Carbon\Carbon::parse($mov->fecha)->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="px-5 py-3 font-medium text-slate-800">
                            {{ $mov->producto->nombre ?? 'Producto #' . $mov->id_producto }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold
                                {{ str_contains($mov->tipo_movimiento, 'venta') ? 'bg-red-50 text-red-700' : '' }}
                                {{ str_contains($mov->tipo_movimiento, 'devolucion') || str_contains($mov->tipo_movimiento, 'restitucion') ? 'bg-emerald-50 text-emerald-700' : '' }}
                                {{ !str_contains($mov->tipo_movimiento, 'venta') && !str_contains($mov->tipo_movimiento, 'devolucion') && !str_contains($mov->tipo_movimiento, 'restitucion') ? 'bg-slate-100 text-slate-700' : '' }}">
                                {{ str_replace('_', ' ', ucfirst($mov->tipo_movimiento)) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 font-bold {{ $mov->cantidad < 0 ? 'text-red-600' : 'text-emerald-600' }}">
                            {{ $mov->cantidad > 0 ? '+' . $mov->cantidad : $mov->cantidad }}
                        </td>
                        <td class="px-5 py-3 text-slate-600">
                            {{ $mov->usuario ? $mov->usuario->nombre . ' ' . $mov->usuario->apellido : 'Sistema' }}
                        </td>
                        <td class="px-5 py-3 text-slate-500 text-xs">
                            {{ $mov->motivo ?: 'Sin motivo registrado' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-400">
                            Aún no se han registrado movimientos de inventario en tu tienda.
                        </td>
                    </tr>
                @endforelse
            </x-table>

            <div class="p-4 border-t border-slate-100">
                {{ $movimientos->links() }}
            </div>
        </x-card>
    </div>
@endsection
