@extends('layouts.app')

@section('title', 'Centro de control VOID')
@section('page-title', 'Centro de control VOID')

@section('content')
    <div class="mb-7">
        <p class="text-sm font-medium uppercase tracking-wider text-blue-600">Administración global</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-900">Bienvenido al centro de control</h1>
        <p class="mt-1 text-sm text-slate-500">Supervisa las empresas registradas y administra sus cuentas desde un único lugar.</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Empresas registradas', 'value' => $estadisticas['empresas']],
            ['label' => 'Empresas activas', 'value' => $estadisticas['empresas_activas']],
            ['label' => 'Administradores', 'value' => $estadisticas['administradores']],
            ['label' => 'Empleados', 'value' => $estadisticas['empleados']],
        ] as $dato)
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">{{ $dato['label'] }}</p>
                <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($dato['value']) }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-8 rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-slate-200 p-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Empresas recientes</h2>
                <p class="text-sm text-slate-500">Estado de cada empresa y volumen de cuentas asociadas.</p>
            </div>
            <a href="{{ route('superadmin.empresas.index') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                Ver todas las empresas
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Empresa</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Administradores</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Empleados</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Estado</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Acción</th>
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
                            <td class="px-5 py-4">
                                @if ($empresa->estado)
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Activa</span>
                                @else
                                    <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">Suspendida</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('superadmin.empresas.show', $empresa) }}" class="font-semibold text-blue-600 hover:text-blue-800">Administrar</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">Todavía no hay empresas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
