@extends('layouts.publico')

@section('title', isset($tiendaSeleccionada) ? 'Catálogo - ' . $tiendaSeleccionada->nombre : 'Catálogo por Tienda')

@section('content')

    @if(isset($tiendaSeleccionada) && $tiendaSeleccionada)
        {{-- Banner de la tienda seleccionada --}}
        <section class="bg-neutral-950 text-amber-50">
            <div class="mx-auto max-w-7xl px-5 py-12 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <a href="{{ route('inicio') }}#tiendas" class="inline-flex items-center gap-2 text-sm text-blue-400 hover:text-blue-300 transition">
                                &larr; Directorio
                            </a>
                            <span class="text-neutral-700">|</span>
                            <a href="{{ route('portal.tienda', $tiendaSeleccionada->id) }}" class="inline-flex items-center gap-2 text-sm text-blue-400 hover:text-blue-300 transition">
                                🏪 Ver perfil y contacto de la tienda
                            </a>
                        </div>
                        <div class="flex items-center gap-4">
                            @if($tiendaSeleccionada->logo)
                                <img src="{{ $tiendaSeleccionada->logo }}" alt="{{ $tiendaSeleccionada->nombre }}" class="h-16 w-16 rounded-2xl object-cover bg-white p-1">
                            @else
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-600 text-3xl font-extrabold text-amber-50">
                                    {{ strtoupper(substr($tiendaSeleccionada->nombre, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <span class="rounded-full bg-blue-500/20 text-blue-400 border border-blue-500/30 px-3 py-1 text-xs font-semibold uppercase tracking-wider">
                                    Catálogo Exclusivo
                                </span>
                                <h1 class="mt-2 text-3xl sm:text-4xl font-extrabold text-amber-50">
                                    {{ $tiendaSeleccionada->nombre }}
                                </h1>
                            </div>
                        </div>
                        <p class="mt-3 max-w-2xl text-slate-300 text-sm">
                            {{ $tiendaSeleccionada->descripcion ?? 'Explora el catálogo de productos disponibles en esta tienda.' }}
                        </p>
                    </div>

                    {{-- Selector rápido de otra tienda --}}
                    @if(isset($tiendas) && $tiendas->count() > 1)
                        <div class="rounded-xl border border-neutral-800 bg-neutral-900 p-4 shrink-0">
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
        <section id="productos" class="py-10 bg-slate-50 min-h-[60vh]">
            <div class="mx-auto max-w-7xl px-5 lg:px-8">

                {{-- Barra de filtros horizontal --}}
                <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm mb-8">
                    <form action="{{ route('catalogo') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                        <input type="hidden" name="tienda" value="{{ $tiendaSeleccionada->id }}">

                        {{-- Búsqueda por texto --}}
                        <div class="lg:col-span-2">
                            <label for="buscar" class="block text-xs font-semibold text-slate-700 uppercase mb-1">
                                Buscar producto
                            </label>
                            <input
                                id="buscar"
                                type="search"
                                name="buscar"
                                value="{{ request('buscar') }}"
                                placeholder="Nombre o palabra clave..."
                                class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-blue-600 focus:bg-white"
                            >
                        </div>

                        {{-- Categoría --}}
                        <div>
                            <label for="categoria" class="block text-xs font-semibold text-slate-700 uppercase mb-1">
                                Categoría
                            </label>
                            <select id="categoria"
                                    name="categoria"
                                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-blue-600 focus:bg-white">
                                <option value="">Todas las categorías</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" @selected((string) request('categoria') === (string) $categoria->id)>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Disponibilidad --}}
                        <div>
                            <label for="disponibilidad" class="block text-xs font-semibold text-slate-700 uppercase mb-1">
                                Disponibilidad
                            </label>
                            <select id="disponibilidad"
                                    name="disponibilidad"
                                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-blue-600 focus:bg-white">
                                <option value="disponibles" @selected(request('disponibilidad', 'disponibles') === 'disponibles')>Solo en existencia</option>
                                <option value="todos" @selected(request('disponibilidad') === 'todos')>Todos los productos</option>
                                <option value="agotados" @selected(request('disponibilidad') === 'agotados')>Agotados</option>
                            </select>
                        </div>

                        {{-- Botones de acción --}}
                        <div class="flex gap-2">
                            <button type="submit"
                                    class="flex-1 rounded-lg bg-neutral-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-600">
                                Filtrar
                            </button>
                            @if(request('buscar') || request('categoria') || request('disponibilidad') || request('precio_min') || request('precio_max'))
                                <a href="{{ route('catalogo', ['tienda' => $tiendaSeleccionada->id]) }}"
                                   class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 flex items-center justify-center">
                                    Limpiar
                                </a>
                            @endif
                        </div>

                        {{-- Rango de precio secundario --}}
                        <div class="sm:col-span-2 lg:col-span-5 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-3 text-xs text-slate-600">
                            <span class="font-semibold uppercase text-slate-700">Rango de precio ($):</span>
                            <input type="number" name="precio_min" value="{{ request('precio_min') }}" placeholder="Min" min="0" step="any"
                                   class="w-24 rounded border border-slate-300 px-2 py-1 bg-slate-50">
                            <span>hasta</span>
                            <input type="number" name="precio_max" value="{{ request('precio_max') }}" placeholder="Max" min="0" step="any"
                                   class="w-24 rounded border border-slate-300 px-2 py-1 bg-slate-50">
                            <span class="text-slate-400">(Aplica al hacer clic en Filtrar)</span>
                        </div>
                    </form>
                </div>

                {{-- Resumen de resultados --}}
                <div class="flex items-center justify-between mb-6">
                    <p class="text-sm text-slate-600 font-medium">
                        Mostrando {{ $productos->count() }} de {{ $productos->total() }} producto(s) en <strong>{{ $tiendaSeleccionada->nombre }}</strong>
                    </p>
                </div>

                {{-- Cuadrícula de productos --}}
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @forelse($productos as $producto)
                        <article class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:border-blue-400 hover:shadow-lg">
                            <a href="{{ route('portal.producto', $producto->id) }}" class="flex h-52 items-center justify-center overflow-hidden bg-slate-50 border-b border-slate-100">
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
                            </a>

                            <div class="flex flex-1 flex-col p-5">
                                <span class="text-xs font-semibold uppercase tracking-wider text-blue-600">
                                    {{ $producto->categoria->nombre ?? 'General' }}
                                </span>

                                <h3 class="mt-2 text-base font-bold text-neutral-950">
                                    <a href="{{ route('portal.producto', $producto->id) }}" class="hover:text-blue-600 transition">
                                        {{ $producto->nombre }}
                                    </a>
                                </h3>

                                <p class="mt-2 line-clamp-2 text-xs leading-5 text-slate-500 flex-1">
                                    {{ $producto->descripcion ?? 'Producto disponible en ' . $tiendaSeleccionada->nombre }}
                                </p>

                                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                                    <span class="text-lg font-extrabold text-neutral-950">
                                        ${{ number_format($producto->precio, 0, ',', '.') }}
                                    </span>

                                    @if($producto->stock > 0)
                                        <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 border border-emerald-200">
                                            Stock: {{ $producto->stock }}
                                        </span>
                                    @else
                                        <span class="rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-semibold text-rose-700 border border-rose-200">
                                            Agotado
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-3 pt-2">
                                    <a href="{{ route('portal.producto', $producto->id) }}"
                                       class="block w-full text-center rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-700 font-semibold text-xs py-2 transition">
                                        Ver detalles &rarr;
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                            <h3 class="text-lg font-bold text-neutral-950">
                                No se encontraron productos con los filtros seleccionados
                            </h3>
                            <p class="mt-2 text-sm text-slate-500">
                                Prueba quitando los filtros o realizando otra búsqueda.
                            </p>
                            <a href="{{ route('catalogo', ['tienda' => $tiendaSeleccionada->id]) }}" class="mt-4 inline-block text-sm font-semibold text-blue-600 hover:underline">
                                Ver todos los productos de {{ $tiendaSeleccionada->nombre }}
                            </a>
                        </div>
                    @endforelse
                </div>

                {{-- Paginación --}}
                <div class="mt-10">
                    {{ $productos->links() }}
                </div>

            </div>
        </section>
    @else
        {{-- Si no seleccionó tienda, invitar a seleccionar una --}}
        <section class="bg-neutral-950 text-amber-50 py-16 text-center">
            <div class="mx-auto max-w-3xl px-5">
                <span class="rounded-full bg-blue-600/30 border border-blue-500/40 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-blue-400">
                    Directorio Multi-Tienda
                </span>
                <h1 class="mt-6 text-4xl font-extrabold sm:text-5xl">
                    Cada tienda tiene su propio catálogo
                </h1>
                <p class="mt-4 text-slate-300">
                    Para consultar los productos y precios exactos, selecciona la tienda cuyo inventario deseas explorar.
                </p>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-5 py-14 lg:px-8">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($tiendas as $tienda)
                    <div class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition">
                        <div>
                            <div class="flex items-center justify-between">
                                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-blue-50 text-2xl font-bold text-blue-600">
                                    {{ strtoupper(substr($tienda->nombre ?? 'T', 0, 1)) }}
                                </div>
                                <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Activa</span>
                            </div>
                            <h3 class="mt-5 text-xl font-bold text-neutral-950">
                                {{ $tienda->nombre }}
                            </h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600 line-clamp-2">
                                {{ $tienda->descripcion ?? 'Explora el catálogo de esta tienda.' }}
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                            <a href="{{ route('portal.tienda', $tienda->id) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900">
                                Ver perfil
                            </a>
                            <a href="{{ route('catalogo', ['tienda' => $tienda->id]) }}" class="text-sm font-bold text-blue-600 hover:text-blue-700">
                                Abrir catálogo &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

@endsection
