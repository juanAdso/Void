@extends('layouts.publico')

@section('title', $tienda->nombre . ' - Perfil Oficial')

@section('content')
    <section class="bg-neutral-950 text-amber-50 border-b border-neutral-800">
        <div class="mx-auto max-w-7xl px-5 py-12 lg:px-8">
            <a href="{{ route('inicio') }}#tiendas" class="inline-flex items-center gap-2 text-sm text-blue-400 hover:text-blue-300 mb-6 transition">
                &larr; Volver al directorio de tiendas
            </a>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
                <div class="flex items-start sm:items-center gap-5">
                    @if($tienda->logo)
                        <img src="{{ $tienda->logo }}" alt="{{ $tienda->nombre }}" class="h-20 w-20 rounded-2xl object-cover bg-white p-1 border border-neutral-700 shadow-md">
                    @else
                        <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-blue-600 text-3xl font-extrabold text-amber-50 shadow-md">
                            {{ strtoupper(substr($tienda->nombre, 0, 1)) }}
                        </div>
                    @endif

                    <div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-3 py-0.5 text-xs font-semibold">
                                Tienda Activa
                            </span>
                            <span class="text-xs text-slate-400">Verificada en VOID</span>
                        </div>
                        <h1 class="mt-2 text-3xl sm:text-4xl font-extrabold text-amber-50">
                            {{ $tienda->nombre }}
                        </h1>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300">
                            {{ $tienda->descripcion ?: 'Bienvenido a la tienda oficial. Conoce nuestros productos y promociones.' }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('catalogo', ['tienda' => $tienda->id]) }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition">
                        Ver catálogo completo &rarr;
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white border-b border-slate-200">
        <div class="mx-auto max-w-7xl px-5 lg:px-8">
            <h2 class="text-lg font-bold text-neutral-950 mb-6">
                Información de contacto y ubicación
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="rounded-2xl border border-slate-200 p-6 bg-slate-50">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                            📞
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900 text-sm">Teléfono / WhatsApp</h3>
                            <p class="text-xs text-slate-500">Atención directa</p>
                        </div>
                    </div>
                    @if($tienda->telefono)
                        <p class="text-base font-bold text-slate-900 mb-3">{{ $tienda->telefono }}</p>
                        <div class="flex gap-2">
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $tienda->telefono) }}" class="inline-block text-xs font-semibold text-blue-600 hover:underline">
                                Llamar ahora
                            </a>
                            <span class="text-slate-300">|</span>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tienda->telefono) }}" target="_blank" rel="noopener" class="inline-block text-xs font-semibold text-emerald-600 hover:underline">
                                Chat WhatsApp
                            </a>
                        </div>
                    @else
                        <p class="text-sm text-slate-400">No especificado</p>
                    @endif
                </div>

                <div class="rounded-2xl border border-slate-200 p-6 bg-slate-50">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                            ✉️
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900 text-sm">Correo Electrónico</h3>
                            <p class="text-xs text-slate-500">Consultas comerciales</p>
                        </div>
                    </div>
                    @if($tienda->correo)
                        <p class="text-base font-bold text-slate-900 mb-3 truncate">{{ $tienda->correo }}</p>
                        <a href="mailto:{{ $tienda->correo }}" class="inline-block text-xs font-semibold text-blue-600 hover:underline">
                            Enviar correo
                        </a>
                    @else
                        <p class="text-sm text-slate-400">No especificado</p>
                    @endif
                </div>

                <div class="rounded-2xl border border-slate-200 p-6 bg-slate-50 sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                            📍
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900 text-sm">Dirección</h3>
                            <p class="text-xs text-slate-500">Punto de atención</p>
                        </div>
                    </div>
                    @if($tienda->direccion)
                        <p class="text-sm font-medium text-slate-800">{{ $tienda->direccion }}</p>
                    @else
                        <p class="text-sm text-slate-400">Ventas en línea / Sin dirección física registrada</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-slate-50">
        <div class="mx-auto max-w-7xl px-5 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Catálogo de la tienda</span>
                    <h2 class="text-2xl font-bold text-neutral-950">Artículos disponibles</h2>
                </div>
                <a href="{{ route('catalogo', ['tienda' => $tienda->id]) }}" class="text-sm font-semibold text-blue-600 hover:underline">
                    Ver todos &rarr;
                </a>
            </div>

            @if(isset($productosDestacados) && $productosDestacados->count())
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($productosDestacados as $prod)
                        <article class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition">
                            <a href="{{ route('portal.producto', $prod->id) }}" class="block h-48 bg-slate-100 border-b border-slate-100 overflow-hidden">
                                @if($prod->imagen)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($prod->imagen) }}" alt="{{ $prod->nombre }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-slate-300">
                                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </a>
                            <div class="flex flex-1 flex-col p-4">
                                <span class="text-xs font-semibold uppercase text-blue-600">{{ $prod->categoria->nombre ?? 'General' }}</span>
                                <a href="{{ route('portal.producto', $prod->id) }}" class="mt-1 font-bold text-slate-900 hover:text-blue-600 transition">
                                    {{ $prod->nombre }}
                                </a>
                                <p class="mt-1 line-clamp-2 text-xs text-slate-500 flex-1">{{ $prod->descripcion }}</p>
                                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                                    <span class="text-lg font-extrabold text-slate-900">${{ number_format($prod->precio, 0, ',', '.') }}</span>
                                    <span class="text-xs font-medium text-slate-500">Stock: {{ $prod->stock }}</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-500 bg-white">
                    Actualmente esta tienda no cuenta con productos activos en inventario.
                </div>
            @endif
        </div>
    </section>
@endsection
