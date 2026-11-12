@extends('layouts.app')
@section('title', 'Superadministrador')
@section('page-title', 'Centro de control de VOID')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
<div class="rounded-xl bg-white p-5 shadow"><p>Tiendas registradas</p><strong>{{ number_format($totalTiendas) }}</strong></div>
<div class="rounded-xl bg-white p-5 shadow"><p>Tiendas activas</p><strong>{{ number_format($tiendasActivas) }}</strong></div>
<div class="rounded-xl bg-white p-5 shadow"><p>Usuarios</p><strong>{{ number_format($totalUsuarios) }}</strong></div>
<div class="rounded-xl bg-white p-5 shadow"><p>Productos</p><strong>{{ number_format($totalProductos) }}</strong></div>
<div class="rounded-xl bg-white p-5 shadow"><p>Ventas pagadas</p><strong>{{ number_format($ventasPagadas) }}</strong></div>
<div class="rounded-xl bg-white p-5 shadow"><p>Ingresos globales</p><strong>${{ number_format($ingresosGlobales, 2) }}</strong></div>
</div>
<div class="mt-6 rounded-xl bg-white p-5 shadow"><h2 class="font-semibold mb-4">Empresas registradas</h2><div class="overflow-x-auto"><table class="min-w-full text-sm"><thead><tr><th class="p-3 text-left">Tienda</th><th class="p-3 text-left">Estado</th><th class="p-3 text-left">Productos</th><th class="p-3 text-left">Usuarios</th><th class="p-3">Acción</th></tr></thead><tbody>
@forelse ($tiendas as $tienda)
<tr><td class="p-3">{{ $tienda->nombre }}</td><td class="p-3">{{ $tienda->estado ? 'Activa' : 'Inactiva' }}</td><td class="p-3">{{ $tienda->productos_count }}</td><td class="p-3">{{ $tienda->usuarios_count }}</td><td class="p-3"><a class="text-blue-600 underline" href="{{ route('tiendas.show', $tienda->id) }}">Consultar</a></td></tr>
@empty
<tr><td colspan="5" class="p-6 text-center">No hay tiendas registradas.</td></tr>
@endforelse
</tbody></table></div></div>
@endsection
