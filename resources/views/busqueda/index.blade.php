@extends('layouts.app')

@section('title', 'Búsqueda global')
@section('page-title', 'Búsqueda global')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div>
            <h2 class="text-2xl font-semibold text-slate-800">Resultados de búsqueda</h2>
            <p class="mt-1 text-sm text-slate-500">
                Busca información de los módulos administrativos disponibles para tu cuenta.
            </p>
        </div>

        @if ($termino === '')
            <div class="rounded-xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                <h3 class="text-lg font-semibold text-slate-700">¿Qué necesitas encontrar?</h3>
                <p class="mt-2 text-sm text-slate-500">
                    Escribe al menos dos caracteres en el buscador de la barra superior.
                </p>
            </div>
        @elseif (mb_strlen($termino) < 2)
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
                Escribe al menos dos caracteres para realizar la búsqueda.
            </div>
        @elseif ($totalResultados === 0)
            <div class="rounded-xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                <h3 class="text-lg font-semibold text-slate-700">No encontramos resultados</h3>
                <p class="mt-2 text-sm text-slate-500">
                    No hay coincidencias para <strong>{{ $termino }}</strong> en los módulos a los que tienes acceso.
                </p>
                <p class="mt-1 text-xs text-slate-400">
                    Comprueba la escritura o intenta con otro nombre, documento o número de factura.
                </p>
            </div>
        @else
            <div class="flex flex-wrap items-center justify-between gap-2">
                <p class="text-sm text-slate-600">
                    Se encontraron <strong>{{ $totalResultados }}</strong> resultados para
                    <strong>{{ $termino }}</strong>.
                </p>
                <span class="text-xs text-slate-400">Máximo 8 resultados por módulo</span>
            </div>

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                @foreach ($secciones as $seccion)
                    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                            <h3 class="font-semibold text-slate-800">{{ $seccion['titulo'] }}</h3>
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                {{ count($seccion['items']) }}
                            </span>
                        </div>

                        <div class="divide-y divide-slate-100">
                            @foreach ($seccion['items'] as $item)
                                <a href="{{ $item['url'] }}"
                                    class="group flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-slate-50">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium text-slate-800 group-hover:text-primary-700">
                                            {{ $item['titulo'] }}
                                        </p>
                                        <p class="mt-1 break-words text-xs text-slate-500">
                                            {{ $item['detalle'] }}
                                        </p>
                                    </div>
                                    <span class="shrink-0 text-xs font-medium text-primary-600">
                                        Abrir módulo
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        @endif
    </div>
@endsection
