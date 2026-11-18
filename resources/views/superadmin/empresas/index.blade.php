@extends('layouts.app')

@section('title', 'Empresas')
@section('page-title', 'Empresas registradas')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Empresas registradas</h1>
        <p class="mt-1 text-sm text-slate-500">Consulta las cuentas, los recursos y el estado de cada empresa de VOID.</p>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="datatable min-w-full divide-y divide-slate-200" data-datatable-paging="false">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Empresa
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Administradores</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Empleados</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Productos</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Clientes</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Estado
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500" data-searchable="false" data-orderable="false">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($empresas as $empresa)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-4">
                                <p class="font-semibold text-slate-800">{{ $empresa->nombre }}</p>
                                <p class="text-xs text-slate-500">{{ $empresa->correo ?: 'Sin correo de contacto' }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $empresa->administradores_count }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $empresa->empleados_count }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $empresa->productos_count }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $empresa->clientes_count }}</td>
                            <td class="px-5 py-4">
                                @if ($empresa->estado)
                                    <span
                                        class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Activa</span>
                                @else
                                    <span
                                        class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">Suspendida</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <a href="{{ route('superadmin.empresas.show', $empresa) }}"
                                    class="font-semibold text-blue-600 hover:text-blue-800">Ver y administrar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">No hay empresas
                                registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4">{{ $empresas->links() }}</div>
    </div>
@endsection
