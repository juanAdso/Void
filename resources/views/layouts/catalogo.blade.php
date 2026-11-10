@extends('layouts.publico')

@section('title', isset($tiendaSeleccionada) ? 'Catálogo - ' . $tiendaSeleccionada->nombre : 'Catálogo por Tienda')

@section('content')

    @if(isset($tiendaSeleccionada) && $tiendaSeleccionada)
        {{-- Banner de la tienda seleccionada --}}
        <section class="bg-neutral-950 text-amber-50">
            <div class="mx-auto max-w-7xl px-5 py-12 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <a href="{{ route('inicio') }}#tiendas" class="inline-flex items-center gap-2 text-sm text-blue-400 hover:text-blue-300 mb-4 transition">
                            &larr; Volver a todas las tiendas
                        </a>
                        <div class="flex items-center gap-4">
                            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-600 text-3xl font-extrabold text-amber-50">
                                {{ strtoupper(substr($tiendaSeleccionada->nombre, 0, 1)) }}
                            </div>
                            <div>
                                <span class="rounded-full bg-blue-500/20 text-blue-400 border border-blue-500/30 px-3 py-1 text-xs font-semibold uppercase tracking-wider">
                                    Catálogo Oficial
                                </span>
                                <h1 class="mt-2 text-3xl sm:text-4xl font-extrabold text-amber-50">
                                    {{ $tiendaSeleccionada->nombre }}
                                </h1>
                            </div>
                        </div>
                        <p class="mt-3 max-w-2xl text-slate-300">
                            {{ $tiendaSeleccionada->descripcion ?? 'Explora el catálogo de productos disponibles en esta tienda.' }}
                        </p>
                    </div>

                    {{-- Selector rápido de otra tienda --}}
                    @if(isset($tiendas) && $tiendas->count() > 1)
                        <div class="rounded-xl border border-neutral-800 bg-neutral-900 p-4">
                            <label for="cambiar-tienda" class="block text-xs font-semibold uppercase text-slate-400 mb-2">
                                Cambiar de tienda:
                            </label>
                            <select id="cambiar-tienda"
                                    onchange="window.location.href = '{{ route('catalogo') }}?tienda=' + this.value"
                                    class="rounded-lg border border-neutral-700 bg-neutral-800 px-3 py-2 text-sm text-amber-50 outline-none focus:border-blue-500">
                                @foreach($tiendas as $t)
                                    <option value="{{ $t->id }}" @selected($t->id === $tiendaSeleccionada->id)>
                                        {{ $t->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Buscador y filtros de la tienda --}}
        <section id="productos" class="py-12 bg-amber-50/40 min-h-[60vh]">
            <div class="mx-auto max-w-7xl px-5 lg:px-8">

                <div class="flex flex-col justify-between gap-5 md:flex-row md:items-end pb-8 border-b border-slate-200">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-widest text-blue-600">
                            Inventario disponible
                        </p>
                        <h2 class="mt-1 text-2xl sm:text-3xl font-extrabold text-neutral-950">
                            Productos de {{ $tiendaSeleccionada->nombre }}
                        </h2>
                        <p class="mt-1 text-sm text-slate-600">
                            Mostrando {{ $productos->count() }} producto(s) en existencia.
                        </p>
                    </div>

                    {{-- Formulario de Búsqueda --}}
                    <form action="{{ route('catalogo') }}" method="GET" class="flex w-full gap-2 md:max-w-md">
                        <input type="hidden" name="tienda" value="{{ $tiendaSeleccionada->id }}">

                        @if(request('categoria'))
                            <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                        @endif

                        <input
                            type="search"
                            name="buscar"
                            value="{{ request('buscar') }}"
                            placeholder="Buscar en {{ $tiendaSeleccionada->nombre }}..."
                            class="min-w-0 flex-1 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                        >

                        <button type="submit"
                                class="rounded-lg bg-neutral-950 px-5 py-2.5 text-sm font-semibold text-amber-50 transition hover:bg-blue-600">
                            Buscar
                        </button>
                    </form>
                </div>

                {{-- Filtro por categorías de la tienda --}}
                @if(isset($categorias) && $categorias->count())
                    <form action="{{ route('catalogo') }}" method="GET" class="mt-6 flex flex-wrap items-center gap-3">
                        <input type="hidden" name="tienda" value="{{ $tiendaSeleccionada->id }}">
                        @if(request('buscar'))
                            <input type="hidden" name="buscar" value="{{ request('buscar') }}">
                        @endif

                        <label for="categoria" class="text-sm font-medium text-slate-700">
                            Categoría:
                        </label>

                        <select id="categoria"
                                name="categoria"
                                onchange="this.form.submit()"
                                class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-600">
                            <option value="">Todas las categorías</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}" @selected((string) request('categoria') === (string) $categoria->id)>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @if(request('categoria') || request('buscar'))
                            <a href="{{ route('catalogo') }}?tienda={{ $tiendaSeleccionada->id }}" class="text-xs text-blue-600 hover:underline">
                                Limpiar filtros
                            </a>
                        @endif
                    </form>
                @endif

                {{-- Cuadrícula de productos --}}
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @forelse($productos as $producto)
                        <article class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:border-blue-400 hover:shadow-lg">
                            <div class="flex h-52 items-center justify-center overflow-hidden bg-slate-50 border-b border-slate-100">
                                @if($producto->imagen)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($producto->imagen) }}"
                                         alt="{{ $producto->nombre }}"
                                         loading="lazy"
                                         class="h-full w-full object-cover">
                                @else
                                    <div class="flex flex-col items-center gap-2 text-slate-400">
                                        <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-xs">Sin imagen</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-1 flex-col p-5">
                                <span class="text-xs font-semibold uppercase tracking-wider text-blue-600">
                                    {{ $producto->categoria->nombre ?? 'General' }}
                                </span>

                                <h3 class="mt-2 text-lg font-bold text-neutral-950">
                                    {{ $producto->nombre }}
                                </h3>

                                <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600 flex-1">
                                    {{ $producto->descripcion ?? 'Producto disponible en ' . $tiendaSeleccionada->nombre }}
                                </p>

                                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                                    <span class="text-xl font-extrabold text-neutral-950">
                                        ${{ number_format($producto->precio, 0, ',', '.') }}
                                    </span>

                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 border border-blue-200">
                                        Stock: {{ $producto->stock }}
                                    </span>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                            <h3 class="text-lg font-bold text-neutral-950">
                                No se encontraron productos en esta tienda
                            </h3>
                            <p class="mt-2 text-sm text-slate-500">
                                Prueba quitando los filtros o realizando otra búsqueda.
                            </p>
                            <a href="{{ route('catalogo') }}?tienda={{ $tiendaSeleccionada->id }}" class="mt-4 inline-block text-sm font-semibold text-blue-600 hover:underline">
                                Ver todos los productos de {{ $tiendaSeleccionada->nombre }}
                            </a>
                        </div>
                    @endforelse
                </div>

            </div>
        </section>
    @else
        {{-- Si no seleccionó tienda, invitar a seleccionar una --}}
        <section class="bg-neutral-950 text-amber-50 py-16 text-center">
            <div class="mx-auto max-w-3xl px-5">
                <span class="rounded-full bg-blue-600/30 border border-blue-500/40 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-blue-400">
                    Selecciona una tienda
                </span>
                <h1 class="mt-6 text-4xl font-extrabold sm:text-5xl">
                    Cada tienda tiene su propio catálogo
                </h1>
                <p class="mt-4 text-slate-300">
                    Para consultar los productos, selecciona primero la tienda cuyo inventario deseas explorar.
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 py-14 lg:px-8">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($tiendas as $tienda)
                    <a href="{{ route('catalogo') }}?tienda={{ $tienda->id }}"
                       class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-blue-500 hover:shadow-md">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-50 text-2xl font-bold text-blue-600 group-hover:bg-blue-600 group-hover:text-amber-50 transition">
                            {{ strtoupper(substr($tienda->nombre ?? 'T', 0, 1)) }}
                        </div>
                        <h3 class="mt-5 text-xl font-bold text-neutral-950 group-hover:text-blue-600 transition">
                            {{ $tienda->nombre }}
                        </h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            {{ $tienda->descripcion ?? 'Explora el catálogo de esta tienda.' }}
                        </p>
                        <span class="mt-4 inline-block text-sm font-semibold text-blue-600">
                            Explorar catálogo &rarr;
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

@endsection
