@extends('layouts.app')

@section('title', 'Detalle de tienda')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <a href="{{ route('tiendas.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-800">&larr; Volver a tiendas</a>
            <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-800">{{ $tienda->nombre }}</h1>
                    <p class="mt-1 text-sm text-slate-500">Información y datos de contacto de la tienda.</p>
                </div>
                <a href="{{ route('tiendas.edit', $tienda->id) }}" class="inline-flex justify-center rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Editar tienda</a>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            @if ($tienda->logo)
                <div class="border-b border-slate-100 bg-slate-50 p-6">
                    <img src="{{ $tienda->logo }}" alt="Logo de {{ $tienda->nombre }}" class="h-24 max-w-full object-contain" onerror="this.style.display='none'">
                </div>
            @endif
            <dl class="grid grid-cols-1 gap-px bg-slate-100 sm:grid-cols-2">
                <div class="bg-white p-5">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Nombre</dt>
                    <dd class="mt-2 font-medium text-slate-800">{{ $tienda->nombre }}</dd>
                </div>
                <div class="bg-white p-5">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</dt>
                    <dd class="mt-2">
                        @if ($tienda->estado)
                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-800">Activa</span>
                        @else
                            <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-800">Inactiva</span>
                        @endif
                    </dd>
                </div>
                <div class="bg-white p-5 sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Descripción</dt>
                    <dd class="mt-2 whitespace-pre-line text-slate-700">{{ $tienda->descripcion ?: 'Sin descripción registrada' }}</dd>
                </div>
                <div class="bg-white p-5">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Dirección</dt>
                    <dd class="mt-2 text-slate-700">{{ $tienda->direccion ?: 'No registrada' }}</dd>
                </div>
                <div class="bg-white p-5">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Teléfono</dt>
                    <dd class="mt-2 text-slate-700">{{ $tienda->telefono ?: 'No registrado' }}</dd>
                </div>
                <div class="bg-white p-5 sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Correo electrónico</dt>
                    <dd class="mt-2 text-slate-700">{{ $tienda->correo ?: 'No registrado' }}</dd>
                </div>
            </dl>
        </div>
    </div>
@endsection
