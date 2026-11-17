@php($panel = $panel ?? session('relaciones_bloqueo'))

@if (!empty($panel['grupos']))
    <section class="mb-6 overflow-hidden rounded-2xl border border-amber-700/50 bg-neutral-900 shadow-xl shadow-black/20"
        role="alert" aria-labelledby="relaciones-bloqueo-titulo">
        <div class="flex items-start gap-3 border-b border-neutral-800 bg-amber-950/20 px-5 py-4">
            <div
                class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-amber-700/50 bg-amber-500/10 text-amber-300">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M12 9v4m0 4h.01M10.3 3.9 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3l-7.5-13.1a2 2 0 0 0-3.4 0Z" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <h2 id="relaciones-bloqueo-titulo" class="text-base font-semibold text-amber-200">
                    {{ $panel['titulo'] ?? 'No se puede eliminar este registro' }}</h2>
                <p class="mt-1 text-sm leading-6 text-neutral-300">
                    {{ $panel['mensaje'] ?? 'Hay otros registros relacionados que deben revisarse antes de continuar.' }}
                </p>
            </div>
        </div>
        <div class="space-y-4 p-4 sm:p-5">
            @foreach ($panel['grupos'] as $grupo)
                <div class="rounded-xl border border-neutral-800 bg-neutral-950/70">
                    <div
                        class="flex flex-wrap items-center justify-between gap-2 border-b border-neutral-800 px-4 py-3">
                        <h3 class="text-sm font-semibold text-neutral-100">{{ $grupo['titulo'] }}</h3>
                        <span
                            class="rounded-full border border-blue-500/30 bg-blue-500/10 px-2.5 py-1 text-xs font-semibold text-blue-300">{{ $grupo['total'] }}
                            {{ $grupo['total'] === 1 ? 'registro' : 'registros' }}</span>
                    </div>
                    <div class="px-4 py-3">
                        <p class="mb-3 text-sm leading-6 text-neutral-400">{{ $grupo['descripcion'] }}</p>
                        @if (!empty($grupo['items']))
                            <div class="divide-y divide-neutral-800">
                                @foreach ($grupo['items'] as $item)
                                    <div
                                        class="flex flex-col gap-2 py-3 first:pt-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-neutral-200">{{ $item['titulo'] }}</p>
                                            <p class="mt-0.5 break-words text-xs leading-5 text-neutral-500">
                                                {{ $item['detalle'] }}</p>
                                        </div>
                                        @if (!empty($item['acciones']))
                                            <div
                                                class="flex shrink-0 flex-wrap items-center gap-2 self-start sm:self-center">
                                                @foreach ($item['acciones'] as $accion)
                                                    <a href="{{ $accion['url'] }}"
                                                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-blue-500/30 bg-blue-500/10 px-3 py-2 text-xs font-semibold text-blue-300 transition hover:border-blue-400/60 hover:bg-blue-500/20 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $accion['texto'] }}
                                                        <span aria-hidden="true">→</span></a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @if ($grupo['total'] > count($grupo['items']))
                                <p class="mt-3 border-t border-neutral-800 pt-3 text-xs text-neutral-500">Se muestran
                                    {{ count($grupo['items']) }} de {{ $grupo['total'] }} registros. Consulta el
                                    módulo para ver el listado completo.</p>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif
