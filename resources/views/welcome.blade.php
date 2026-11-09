@extends('layouts.publico')

@section('title', 'Explora tiendas y catálogos')

@section('content')

    {{-- Presentación principal --}}
    <section class="bg-neutral-950 text-amber-50">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-5 py-16 md:grid-cols-2 md:py-24 lg:px-8">

            <div>
                <span class="inline-block rounded-full border border-blue-600 bg-blue-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-amber-50">
                    Catálogos por tienda
                </span>

                <h1 class="mt-6 text-4xl font-extrabold leading-tight sm:text-5xl lg:text-6xl">
                    <span class="text-amber-50">Descubre lo que</span>
                    <span class="text-blue-500">cada tienda</span>
                    <span class="text-amber-50">tiene para ti.</span>
                </h1>

                <p class="mt-5 max-w-xl leading-7 text-slate-300">
                    Cada tienda cuenta con su propio catálogo, productos, precios e inventario. Selecciona una tienda para explorar sus artículos disponibles.
                </p>

                <a href="#tiendas"
                   class="mt-8 inline-flex rounded-lg bg-blue-600 px-6 py-3 font-semibold text-amber-50 transition hover:bg-blue-500">
                    Explorar tiendas
                </a>
            </div>

            <div class="relative">
                <div class="rounded-3xl border border-neutral-800 bg-neutral-900 p-6 sm:p-8">
                    <p class="text-sm font-semibold uppercase tracking-widest text-blue-400">
                        BIENVENIDO A VOID
                    </p>

                    <p class="mt-4 text-3xl font-bold text-amber-50">
                        Tu próxima búsqueda empieza aquí.
                    </p>

                    <p class="mt-4 leading-7 text-slate-300">
                        Selecciona una tienda para acceder directamente a su catálogo y consultar sus productos exclusivos.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <span class="rounded-full bg-neutral-800 border border-neutral-700 px-4 py-2 text-sm text-amber-50">
                            Tiendas oficiales
                        </span>
                        <span class="rounded-full bg-neutral-800 border border-neutral-700 px-4 py-2 text-sm text-amber-50">
                            Catálogo exclusivo
                        </span>
                        <span class="rounded-full bg-neutral-800 border border-neutral-700 px-4 py-2 text-sm text-amber-50">
                            Disponibilidad en tiempo real
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- Tiendas disponibles --}}
    <section id="tiendas" class="mx-auto max-w-7xl scroll-mt-24 px-5 py-14 lg:px-8">

        <div class="mb-8">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-600">
                Directorio
            </p>

            <h2 class="mt-2 text-3xl font-extrabold text-neutral-950">
                Encuentra una tienda
            </h2>

            <p class="mt-3 text-slate-600">
                Selecciona una tienda para ingresar a su catálogo y consultar únicamente sus productos.
            </p>
        </div>

        @if(isset($tiendas) && $tiendas->count())

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

                @foreach($tiendas as $tienda)

                    <a href="{{ route('catalogo') }}?tienda={{ $tienda->id }}"
                       class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-blue-500 hover:shadow-md">

                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-50 text-2xl font-bold text-blue-600 group-hover:bg-blue-600 group-hover:text-amber-50 transition">
                            {{ strtoupper(substr($tienda->nombre ?? 'T', 0, 1)) }}
                        </div>

                        <h3 class="mt-5 text-xl font-bold text-neutral-950 group-hover:text-blue-600 transition">
                            {{ $tienda->nombre ?? 'Tienda' }}
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-600 line-clamp-2">
                            {{ $tienda->descripcion ?? 'Explora el catálogo y productos de esta tienda.' }}
                        </p>

                        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs text-slate-400">
                                {{ $tienda->ciudad ?? 'Colombia' }}
                            </span>
                            <span class="text-sm font-semibold text-blue-600 group-hover:translate-x-1 transition">
                                Ver catálogo &rarr;
                            </span>
                        </div>
                    </a>

                @endforeach

            </div>

        @else

            <div class="rounded-2xl border border-dashed border-slate-300 bg-amber-50/50 px-6 py-12 text-center">
                <h3 class="font-bold text-neutral-950 text-lg">
                    Próximamente encontrarás tiendas aquí
                </h3>

                <p class="mt-2 text-sm text-slate-600">
                    Las tiendas aparecerán cuando estén registradas en el sistema.
                </p>
            </div>

        @endif
    </section>

@endsection
