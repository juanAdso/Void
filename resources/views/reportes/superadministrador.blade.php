@extends('layouts.app')
@section('title', 'Reportes globales - VOID')
@section('page-title', 'Reportes globales')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<div class="rounded-xl bg-white p-5 shadow"><p>Ventas pagadas</p><strong>{{ number_format($ventasGlobales) }}</strong></div>
<div class="rounded-xl bg-white p-5 shadow"><p>Ingresos globales</p><strong>${{ number_format($ingresosGlobales, 2) }}</strong></div>
</div>
<div class="mt-6 rounded-xl bg-white p-5 shadow"><h2 class="font-semibold mb-4">Rendimiento por tienda</h2><div class="overflow-x-auto"><table class="min-w-full text-sm"><thead><tr><th class="p-3 text-left">Tienda</th><th class="p-3 text-left">Ventas pagadas</th><th class="p-3 text-left">Ingresos</th></tr></thead><tbody>
@forelse ($ventasPorTienda as $fila)
<tr><td class="p-3">{{ $fila->nombre }}</td><td class="p-3">{{ number_format((int) $fila->ventas_pagadas) }}</td><td class="p-3">${{ number_format((float) $fila->ingresos, 2) }}</td></tr>
@empty
<tr><td colspan="3" class="p-6 text-center">No hay información de ventas.</td></tr>
@endforelse
</tbody></table></div></div>
@endsection
