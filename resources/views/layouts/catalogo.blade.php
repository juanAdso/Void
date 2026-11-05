
@extends('layouts.publico')

@section('title', 'Explora tiendas y productos')

@section('content')

    {{-- Presentación principal --}}
    <section class="bg-neutral-950 text-amber 50">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-5 py-16 md:grid-cols-2 md:py-24 lg:px-8">

            <div>
                <span class="inline-block rounded-full border border-blue-600 bg-blue-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-amber-50">
                    Catálogos en un solo lugar
                </span>

                <h1 class="mt-6 text-4xl font-extrabold leading-tight sm:text-5xl lg:text-6xl">
                    <span class="text-amber-50" >Descubre lo que
                    <span class="text-blue-600">cada tienda</span>
                    tiene para ti.
                </h1>

                <p class="mt-5 max-w-xl leading-7 text-amber-50 ">
                    Explora tiendas, conoce sus productos y consulta
                    sus precios y disponibilidad desde un solo lugar.
                </p>

                <a href="#tiendas"
                   class="mt-8 inline-flex rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white transition hover:bg-blue-500">
                    Explorar tiendas
                </a>
            </div>

            <div class="relative">
                <div class="rounded-3xl border border-amber-50 bg-blue-500 p-6 sm:p-8">
                    <p class="text-lg font-medium text-amber-50">
                        BIENVENIDO A VOID
                    </p>

                    <p class="mt-4 text-3xl text-amber-50 font-bold">
                        Tu próxima búsqueda empieza aquí.
                    </p>

                    <p class="mt-4 leading-7 text-amber-50">
                        Selecciona una tienda para consultar su catálogo
                        y descubrir los productos que ofrece.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <span class="rounded-full bg-amber-50 px-4 py-2 text-sm hover:bg-blue-600">
                            Ropa
                        </span>
                        <span class="rounded-full bg-amber-50 px-4 py-2 text-sm hover:bg-blue-600">
                            Accesorios
                        </span>
                        <span class="rounded-full bg-amber-50 px-4 py-2 text-sm hover:bg-blue-600">
                            Nuevos productos
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
                Explora
            </p>

            <h2 class="mt-2 text-3xl font-extrabold">
                Encuentra una tienda
            </h2>

            <p class="mt-3 text-amber-50">
                Selecciona una tienda para consultar los productos que tiene disponibles.
            </p>
        </div>

        @if(isset($tiendas) && $tiendas->count())

            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

                @foreach($tiendas as $tienda)

                    <a href="{{ url('/catalogo') }}?tienda={{ $tienda->id }}"
                       class="group rounded-2xl border border-amber-50 p-6 transition hover:-translate-y-1 hover:border-blue-500 hover:shadow-lg">

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-xl font-bold text-blue-600">
                            {{ strtoupper(substr($tienda->nombre ?? 'T', 0, 1)) }}
                        </div>

                        <h3 class="mt-5 text-xl font-bold group-hover:text-blue-600">
                            {{ $tienda->nombre ?? 'Tienda' }}
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-neutral-950">
                            {{ $tienda->descripcion ?? 'Explora el catálogo de esta tienda.' }}
                        </p>

                        <span class="mt-5 inline-block text-sm font-semibold text-blue-600">
                            Ver catálogo →
                        </span>
                    </a>

                @endforeach

            </div>

        @else

            <div class="rounded-2xl border border-dashed border-amber-50 px-6 py-10 text-center">
                <h3 class="font-bold text-neutral-950 text-lg">
                    Próximamente encontrarás tiendas aquí
                </h3>

                <p class="mt-2 text-sm text-neutral-950">
                    Las tiendas aparecerán cuando estén registradas y conectadas al catálogo público.
                </p>
            </div>

        @endif
    </section>

    {{-- Buscador y catálogo --}}
    <section id="productos" class="scroll-mt-24 bg-amber-50 py-14">

        <div class="mx-auto max-w-7xl px-5 lg:px-8">

            <div class="flex flex-col justify-between gap-5 md:flex-row md:items-end">

                <div>
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-600">
                        Catálogo
                    </p>

                    <h2 class="mt-2 text-3xl font-extrabold">
                        Explora los productos
                    </h2>

                    <p class="mt-3 text-neutral-950">
                        Busca productos y consulta sus detalles.
                    </p>
                </div>

                {{-- Búsqueda --}}
                <form action="{{ url('/catalogo') }}"
                      method="GET"
                      class="flex w-full gap-2 md:max-w-md">

                    @if(request('tienda'))
                        <input type="hidden" name="tienda" value="{{ request('tienda') }}">
                    @endif

                    @if(request('categoria'))
                        <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                    @endif

                    <input
                        type="search"
                        name="buscar"
                        value="{{ request('buscar') }}"
                        placeholder="Buscar un producto..."
                        aria-label="Buscar productos"
                        class="min-w-0 flex-1 rounded-lg border border-amber-50 bg-amber-50 px-4 py-3 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600 focus:ring-offset-2"
                    >

                    <button type="submit"
                            class="rounded-lg bg-neutral-950 px-5 py-3 font-semibold text-amber-50 transition hover:bg-blue-600">
                        Buscar
                    </button>
                </form>

            </div>

            {{-- Filtro por categoría --}}
            @if(isset($categorias) && $categorias->count())

                <form action="{{ url('/catalogo') }}"
                      method="GET"
                      class="mt-8 flex flex-wrap items-center gap-3">

                    @if(request('tienda'))
                        <input type="hidden" name="tienda" value="{{ request('tienda') }}">
                    @endif

                    @if(request('buscar'))
                        <input type="hidden" name="buscar" value="{{ request('buscar') }}">
                    @endif

                    <label for="categoria" class="text-sm font-medium text-neutral-950">
                        Categoría:
                    </label>

                    <select id="categoria"
                            name="categoria"
                            onchange="this.form.submit()"
                            class="rounded-lg border border-amber-50 bg-amber-50 px-4 py-2.5 outline-none focus:border-blue-600">

                        <option value="">Todas las categorías</option>

                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}"
                                @selected((string) request('categoria') === (string) $categoria->id)>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach

                    </select>
                </form>

            @endif

            {{-- Tarjetas de productos --}}
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

                @forelse($productos ?? collect() as $producto)

                    @if($producto->estado && $producto->stock > 0)

                        <article class="overflow-hidden rounded-2xl border border-amber-50 bg-amber-50 transition hover:-translate-y-1 hover:shadow-lg">

                            {{-- Imagen --}}
                            <div class="flex h-60 items-center justify-center overflow-hidden bg-amber-50">

                                @if($producto->imagen)
                                    <img
                                        src="{{ \Illuminate\Support\Facades\Storage::url($producto->imagen) }}"
                                        alt="{{ $producto->nombre }}"
                                        loading="lazy"
                                        class="h-full w-full object-cover"
                                    >
                                @else
                                    <span class="text-sm text-neutral-950">
                                        Imagen no disponible
                                    </span>
                                @endif

                            </div>

                            <div class="p-5">

                                <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">
                                    {{ $producto->categoria->nombre ?? 'General' }}
                                </p>

                                <h3 class="mt-2 text-lg font-bold">
                                    {{ $producto->nombre }}
                                </h3>

                                <p class="mt-2 line-clamp-3 text-sm leading-6 text-neutral-950">
                                    {{ $producto->descripcion }}
                                </p>

                                <div class="mt-5 flex items-center justify-between gap-3">
                                    <span class="text-xl font-extrabold">
                                        ${{ number_format($producto->precio, 0, ',', '.') }}
                                    </span>

                                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-blue-500">
                                        Disponible
                                    </span>
                                </div>

                            </div>
                        </article>

                    @endif

                @empty

                    <div class="col-span-full rounded-2xl border border-dashed border-amber-50 bg-amber-50 px-6 py-12 text-center">
                        <h3 class="text-lg font-bold">
                            Aún no hay productos para mostrar
                        </h3>

                        <p class="mt-2 text-sm text-neutral-950">
                            Prueba otra búsqueda o vuelve más tarde.
                        </p>
                    </div>

                @endforelse

            </div>

        </div>
    </section>

@endsection
