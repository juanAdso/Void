@extends('layouts.publico')

@section('title', 'Directorio de tiendas y catálogos')

@section('content')

    {{-- Presentación principal --}}
    <section class="bg-neutral-950 text-amber-50">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-5 py-16 md:grid-cols-2 md:py-24 lg:px-8">

            <div>
                <span class="inline-block rounded-full border border-blue-600 bg-blue-600/20 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-blue-400">
                    Portal Multi-Tienda Oficial
                </span>

                <h1 class="mt-6 text-4xl font-extrabold leading-tight sm:text-5xl lg:text-6xl">
                    <span class="text-amber-50">Descubre lo que</span>
                    <span class="text-blue-500">cada tienda</span>
                    <span class="text-amber-50">ofrece.</span>
                </h1>

                <p class="mt-5 max-w-xl leading-7 text-slate-300">
                    Cada comercio registrado administra de manera autónoma sus existencias, precios y promociones. Explora el directorio oficial y accede al catálogo de tu preferencia.
                </p>

                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="#tiendas"
                       class="inline-flex rounded-xl bg-blue-600 px-6 py-3.5 font-semibold text-white transition hover:bg-blue-500 shadow-sm">
                        Explorar directorio
                    </a>
                    <a href="{{ route('registro.tienda') }}"
                       class="inline-flex rounded-xl border border-neutral-700 bg-neutral-900 px-6 py-3.5 font-semibold text-slate-200 transition hover:bg-neutral-800">
                        Registrar mi negocio
                    </a>
                </div>
            </div>

            <div class="relative">
                <div class="rounded-3xl border border-neutral-800 bg-neutral-900/90 p-6 sm:p-8 shadow-xl">
                    <p class="text-xs font-bold uppercase tracking-widest text-blue-400">
                        BIENVENIDO A VOID
                    </p>

                    <p class="mt-3 text-2xl sm:text-3xl font-bold text-amber-50">
                        Catálogos independientes, inventario en tiempo real.
                    </p>

                    <p class="mt-4 leading-7 text-slate-300 text-sm">
                        Consulta productos con existencias actualizadas y contacta directamente a cada comercio para concretar tu compra sin intermediarios.
                    </p>

                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="rounded-xl bg-neutral-800/80 border border-neutral-700 p-3 text-center">
                            <span class="block text-xl font-bold text-blue-400">{{ $tiendas->count() }}</span>
                            <span class="text-xs text-slate-400">Tiendas activas</span>
                        </div>
                        <div class="rounded-xl bg-neutral-800/80 border border-neutral-700 p-3 text-center">
                            <span class="block text-xl font-bold text-emerald-400">100%</span>
                            <span class="text-xs text-slate-400">Verificadas</span>
                        </div>
                        <div class="rounded-xl bg-neutral-800/80 border border-neutral-700 p-3 text-center">
                            <span class="block text-xl font-bold text-amber-400">Directo</span>
                            <span class="text-xs text-slate-400">Sin comisiones</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- Directorio de tiendas disponibles --}}
    <section id="tiendas" class="mx-auto max-w-7xl scroll-mt-24 px-5 py-14 lg:px-8">

        <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-200 pb-6">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-blue-600">
                    Directorio Comercial
                </p>

                <h2 class="mt-2 text-3xl font-extrabold text-neutral-950">
                    Tiendas Registradas
                </h2>

                <p class="mt-2 text-sm text-slate-600">
                    Accede a la ficha o catálogo exclusivo de cualquiera de nuestros comercios aliados.
                </p>
            </div>
            <span class="text-xs text-slate-500 font-medium">
                {{ $tiendas->count() }} tienda(s) disponible(s)
            </span>
        </div>

        @if(isset($tiendas) && $tiendas->count())

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

                @foreach($tiendas as $tienda)

                    <div class="group flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-blue-500 hover:shadow-md">

                        <div>
                            <div class="flex items-center justify-between">
                                @if($tienda->logo)
                                    <img src="{{ $tienda->logo }}" alt="{{ $tienda->nombre }}" class="h-14 w-14 rounded-xl object-cover bg-slate-50 p-1 border border-slate-200">
                                @else
                                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-50 text-2xl font-bold text-blue-600 group-hover:bg-blue-600 group-hover:text-amber-50 transition">
                                        {{ strtoupper(substr($tienda->nombre ?? 'T', 0, 1)) }}
                                    </div>
                                @endif

                                <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 border border-emerald-200">
                                    {{ $tienda->total_productos ?? 0 }} productos
                                </span>
                            </div>

                            <h3 class="mt-5 text-xl font-bold text-neutral-950 group-hover:text-blue-600 transition">
                                <a href="{{ route('portal.tienda', $tienda->id) }}">
                                    {{ $tienda->nombre }}
                                </a>
                            </h3>

                            <p class="mt-2 text-sm leading-6 text-slate-600 line-clamp-2">
                                {{ $tienda->descripcion ?: 'Explora el catálogo y productos disponibles de esta tienda.' }}
                            </p>

                            @if($tienda->telefono || $tienda->correo)
                                <div class="mt-4 flex flex-col gap-1 text-xs text-slate-500">
                                    @if($tienda->telefono)
                                        <span class="truncate">📞 {{ $tienda->telefono }}</span>
                                    @endif
                                    @if($tienda->correo)
                                        <span class="truncate">✉️ {{ $tienda->correo }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                            <a href="{{ route('portal.tienda', $tienda->id) }}" class="text-xs font-semibold text-slate-600 hover:text-blue-600 transition">
                                Ver perfil
                            </a>
                            <a href="{{ route('catalogo', ['tienda' => $tienda->id]) }}" class="inline-flex items-center gap-1 text-sm font-bold text-blue-600 hover:text-blue-800 transition">
                                Ver catálogo &rarr;
                            </a>
                        </div>
                    </div>

                @endforeach

            </div>

        @else

            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-3xl">
                    🏪
                </div>
                <h3 class="mt-4 font-bold text-neutral-950 text-lg">
                    No hay tiendas registradas actualmente
                </h3>
                <p class="mt-2 text-sm text-slate-500 max-w-sm mx-auto">
                    Sé el primero en registrar tu tienda y comenzar a publicar tu inventario comercial.
                </p>
                <a href="{{ route('registro.tienda') }}" class="mt-6 inline-flex rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-500">
                    Registrar mi tienda
                </a>
            </div>

        @endif
    </section>

@endsection
