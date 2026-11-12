@extends('layouts.publico')

@section('title', $producto->nombre . ' - ' . $producto->tienda->nombre)

@section('content')
    <div class="bg-white border-b border-slate-200">
        <div class="mx-auto max-w-7xl px-5 py-6 lg:px-8">
            <nav class="flex items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('inicio') }}" class="hover:text-blue-600">Inicio</a>
                <span>/</span>
                <a href="{{ route('portal.tienda', $producto->tienda->id) }}" class="hover:text-blue-600">{{ $producto->tienda->nombre }}</a>
                <span>/</span>
                <a href="{{ route('catalogo', ['tienda' => $producto->tienda->id]) }}" class="hover:text-blue-600">Catálogo</a>
                <span>/</span>
                <span class="text-slate-900 font-medium truncate">{{ $producto->nombre }}</span>
            </nav>
        </div>
    </div>

    <section class="py-12 bg-slate-50">
        <div class="mx-auto max-w-7xl px-5 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 bg-white rounded-3xl border border-slate-200 p-6 sm:p-10 shadow-sm">
                <div class="flex flex-col items-center justify-center bg-slate-50 rounded-2xl border border-slate-100 overflow-hidden min-h-[350px]">
                    @if($producto->imagen)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($producto->imagen) }}"
                             alt="{{ $producto->nombre }}"
                             class="h-full w-full max-h-[500px] object-contain">
                    @else
                        <div class="flex flex-col items-center gap-3 text-slate-400 p-12">
                            <svg class="h-24 w-24 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm font-medium">Imagen no disponible</span>
                        </div>
                    @endif
                </div>

                <div class="flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-4">
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-blue-700 border border-blue-200">
                                {{ $producto->categoria->nombre ?? 'General' }}
                            </span>

                            <a href="{{ route('portal.tienda', $producto->tienda->id) }}" class="flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-blue-600">
                                <span>🏪 {{ $producto->tienda->nombre }}</span>
                            </a>
                        </div>

                        <h1 class="mt-4 text-3xl font-extrabold text-neutral-950 sm:text-4xl">
                            {{ $producto->nombre }}
                        </h1>

                        <div class="mt-6 flex items-baseline gap-4">
                            <span class="text-4xl font-black text-neutral-950">
                                ${{ number_format($producto->precio, 0, ',', '.') }}
                            </span>
                            @if($producto->stock > 0)
                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">
                                    En Stock ({{ $producto->stock }} disponibles)
                                </span>
                            @else
                                <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-800">
                                    Agotado
                                </span>
                            @endif
                        </div>

                        <div class="mt-6 border-t border-slate-100 pt-6">
                            <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500">
                                Descripción del producto
                            </h2>
                            <p class="mt-3 text-base leading-7 text-slate-700 whitespace-pre-line">
                                {{ $producto->descripcion ?: 'Este artículo no cuenta con descripción detallada en este momento.' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-8 rounded-2xl bg-slate-50 border border-slate-200 p-6">
                        <h3 class="text-sm font-bold text-slate-900 mb-2">
                            ¿Deseas adquirir este producto?
                        </h3>
                        <p class="text-xs text-slate-600 mb-4">
                            Contacta directamente a la tienda <strong>{{ $producto->tienda->nombre }}</strong> para coordinar tu compra y despacho.
                        </p>

                        <div class="flex flex-wrap items-center gap-3">
                            @if($producto->tienda->telefono)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $producto->tienda->telefono) }}?text={{ urlencode('Hola, me interesa el producto ' . $producto->nombre . ' de su catálogo VOID.') }}"
                                   target="_blank"
                                   rel="noopener"
                                   class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-500 transition shadow-sm">
                                    <span>💬 Contactar por WhatsApp</span>
                                </a>
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $producto->tienda->telefono) }}"
                                   class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    <span>📞 {{ $producto->tienda->telefono }}</span>
                                </a>
                            @endif

                            @if($producto->tienda->correo)
                                <a href="mailto:{{ $producto->tienda->correo }}?subject={{ urlencode('Consulta producto: ' . $producto->nombre) }}"
                                   class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                                    <span>✉️ Enviar correo</span>
                                </a>
                            @endif

                            <a href="{{ route('portal.tienda', $producto->tienda->id) }}"
                               class="inline-flex items-center gap-2 text-xs font-bold text-blue-600 hover:underline ml-auto">
                                Ver ficha de la tienda &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($productosRelacionados) && $productosRelacionados->count())
                <div class="mt-14">
                    <h2 class="text-xl font-bold text-neutral-950 mb-6">
                        Más productos en {{ $producto->categoria->nombre ?? 'esta categoría' }}
                    </h2>
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach($productosRelacionados as $rel)
                            <article class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition">
                                <a href="{{ route('portal.producto', $rel->id) }}" class="block h-44 bg-slate-50 border-b border-slate-100 overflow-hidden">
                                    @if($rel->imagen)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($rel->imagen) }}" alt="{{ $rel->nombre }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-slate-300">
                                            <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </a>
                                <div class="flex flex-1 flex-col p-4">
                                    <a href="{{ route('portal.producto', $rel->id) }}" class="font-bold text-slate-900 hover:text-blue-600 transition text-sm">
                                        {{ $rel->nombre }}
                                    </a>
                                    <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-2">
                                        <span class="font-extrabold text-slate-900 text-base">${{ number_format($rel->precio, 0, ',', '.') }}</span>
                                        <span class="text-xs text-slate-500">Stock: {{ $rel->stock }}</span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
