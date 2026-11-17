@extends('layouts.app')

@section('title', 'Factura #' . $factura->id)
@section('page-title', 'Consulta de factura #' . $factura->id)

@section('content')
    <div class="mx-auto max-w-7xl space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="void-page-eyebrow">Facturación / Consulta</p>
                <h2 class="mt-2 text-2xl font-bold text-neutral-50 sm:text-3xl">Factura #{{ $factura->id }}</h2>
                <p class="mt-1 text-sm text-neutral-400">Información general, productos y valores de la factura en un solo
                    lugar.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('facturas.index') }}"
                    class="rounded-lg border border-neutral-700 px-4 py-2 text-sm font-semibold text-neutral-200 transition hover:bg-neutral-800">Volver</a>
                @can('editar facturas')
                    @if ($factura->estado !== 'anulada')
                        <a href="{{ route('facturas.edit', $factura->id) }}"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">Editar
                            factura</a>
                    @endif
                @endcan
                @can('eliminar facturas')
                    @if ($factura->estado === 'pendiente')
                        <form action="{{ route('facturas.destroy', $factura->id) }}" method="POST"
                            onsubmit="return confirm('¿Eliminar la factura #{{ $factura->id }}? Se eliminarán sus productos asociados, se devolverán todas las unidades al inventario y se registrarán los movimientos correspondientes. Esta operación no se puede deshacer.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700">Eliminar
                                factura</button>
                        </form>
                    @endif
                @endcan
            </div>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-500/40 bg-red-950/30 p-4 text-sm text-red-200" role="alert">
                <p class="mb-2 font-semibold">No se pudo completar la operación:</p>
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-neutral-800 bg-neutral-900/80 p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Cliente</p>
                <p class="mt-2 font-semibold text-neutral-100">
                    {{ trim(($factura->cliente?->nombre ?? '') . ' ' . ($factura->cliente?->apellido ?? '')) ?: 'Cliente no disponible' }}
                </p>
                <p class="mt-1 text-xs text-neutral-500">{{ $factura->cliente?->documento ?? 'Sin documento' }}</p>
            </div>
            <div class="rounded-xl border border-neutral-800 bg-neutral-900/80 p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Fecha</p>
                <p class="mt-2 font-semibold text-neutral-100">{{ $factura->fecha?->format('d/m/Y') ?? 'Sin fecha' }}</p>
                <p class="mt-1 text-xs text-neutral-500">Factura #{{ $factura->id }}</p>
            </div>
            <div class="rounded-xl border border-neutral-800 bg-neutral-900/80 p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Responsable</p>
                <p class="mt-2 font-semibold text-neutral-100">
                    {{ trim(($factura->usuario?->nombre ?? '') . ' ' . ($factura->usuario?->apellido ?? '')) ?: 'Usuario no disponible' }}
                </p>
                <p class="mt-1 text-xs text-neutral-500">Usuario que registró la factura</p>
            </div>
            <div class="rounded-xl border border-neutral-800 bg-neutral-900/80 p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Estado</p>
                <div class="mt-2">
                    @if ($factura->estado === 'pagada')
                        <span
                            class="rounded-full bg-green-500/15 px-3 py-1 text-xs font-semibold text-green-300">Pagada</span>
                    @elseif ($factura->estado === 'pendiente')
                        <span
                            class="rounded-full bg-amber-500/15 px-3 py-1 text-xs font-semibold text-amber-300">Pendiente</span>
                    @else
                        <span class="rounded-full bg-red-500/15 px-3 py-1 text-xs font-semibold text-red-300">Anulada</span>
                    @endif
                </div>
                @can('eliminar facturas')
                    @if ($factura->estado !== 'pendiente')
                        <p class="mt-2 text-xs text-neutral-500">No eliminable: las facturas pagadas o anuladas se conservan.
                        </p>
                    @endif
                @endcan
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-neutral-800 bg-neutral-900/80">
            <div
                class="flex flex-col gap-2 border-b border-neutral-800 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-neutral-100">Productos de la factura</h3>
                    <p class="text-sm text-neutral-400">Los productos, cantidades y valores registrados en esta venta.</p>
                </div>
                <span class="text-xs text-neutral-500">{{ $factura->detalle_factura->count() }} producto(s)
                    asociado(s)</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[900px] w-full">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-wider">Producto</th>
                            <th class="px-4 py-3 text-right text-xs uppercase tracking-wider">Cantidad</th>
                            <th class="px-4 py-3 text-right text-xs uppercase tracking-wider">Precio unitario</th>
                            <th class="px-4 py-3 text-right text-xs uppercase tracking-wider">Subtotal</th>
                            @if ($mostrarAccionesDetalle)
                                <th class="px-4 py-3 text-center text-xs uppercase tracking-wider">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($factura->detalle_factura as $detalle)
                            <tr class="border-t border-neutral-800 align-top">
                                <td class="px-4 py-4">
                                    <p class="font-semibold text-neutral-100">
                                        {{ $detalle->producto?->nombre ?? 'Producto no disponible' }}</p>
                                    <p class="mt-1 text-xs text-neutral-500">Detalle #{{ $detalle->id }}</p>
                                </td>
                                <td class="px-4 py-4 text-right">{{ $detalle->cantidad }}</td>
                                <td class="px-4 py-4 text-right">$
                                    {{ number_format((float) $detalle->precio_unitario, 2, ',', '.') }}</td>
                                <td class="px-4 py-4 text-right font-semibold">$
                                    {{ number_format((float) $detalle->subtotal, 2, ',', '.') }}</td>
                                @if ($mostrarAccionesDetalle)
                                    <td class="px-4 py-4">
                                        <div class="flex min-w-[170px] flex-col items-center gap-2">
                                            @if ($puedeEditarDetalle)
                                                <details
                                                    class="w-full max-w-[260px] rounded-lg border border-blue-500/30 bg-blue-500/5 p-2">
                                                    <summary
                                                        class="cursor-pointer text-center text-xs font-semibold text-blue-300">
                                                        Editar producto</summary>
                                                    <form action="{{ route('detalle_facturas.update', $detalle->id) }}"
                                                        method="POST" class="mt-3 space-y-3">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="id_factura"
                                                            value="{{ $factura->id }}">
                                                        <div>
                                                            <label
                                                                class="mb-1 block text-xs text-neutral-300">Producto</label>
                                                            <select name="id_producto" required
                                                                class="w-full rounded-lg border px-2 py-2 text-sm">
                                                                @if (!$productos->contains('id', $detalle->id_producto))
                                                                    <option value="" selected disabled>Selecciona un
                                                                        producto activo</option>
                                                                @endif
                                                                @foreach ($productos as $producto)
                                                                    <option value="{{ $producto->id }}"
                                                                        data-price="{{ $producto->precio }}"
                                                                        @selected(old('id_producto', $detalle->id_producto) == $producto->id)>
                                                                        {{ $producto->nombre }} · Stock:
                                                                        {{ $producto->stock }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="mb-1 block text-xs text-neutral-300">Cantidad</label>
                                                            <input type="number" name="cantidad" min="1" required
                                                                value="{{ old('cantidad', $detalle->cantidad) }}"
                                                                class="w-full rounded-lg border px-2 py-2 text-sm">
                                                        </div>
                                                        <div>
                                                            <label class="mb-1 block text-xs text-neutral-300">Precio
                                                                unitario</label>
                                                            <input type="number" name="precio_unitario" min="0"
                                                                step="0.01" required
                                                                value="{{ old('precio_unitario', $detalle->precio_unitario) }}"
                                                                class="w-full rounded-lg border px-2 py-2 text-sm">
                                                        </div>
                                                        <button type="submit"
                                                            class="w-full rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">Guardar
                                                            cambios</button>
                                                    </form>
                                                </details>
                                            @endif
                                            @if ($puedeEliminarDetalle)
                                                <form action="{{ route('detalle_facturas.destroy', $detalle->id) }}"
                                                    method="POST" class="w-full"
                                                    onsubmit="return confirm('¿Retirar el producto de la factura #{{ $factura->id }}? Las unidades regresarán al inventario y se registrará el movimiento.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="w-full rounded-lg bg-red-600/15 px-3 py-2 text-xs font-semibold text-red-300 transition hover:bg-red-600/25">Eliminar
                                                        producto</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $mostrarAccionesDetalle ? 5 : 4 }}"
                                    class="px-5 py-10 text-center text-sm text-neutral-400">
                                    Esta factura todavía no tiene productos asociados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-neutral-800 bg-neutral-950/40 p-5">
                <div class="ml-auto max-w-sm space-y-2 text-sm">
                    <div class="flex justify-between gap-4 text-neutral-400">
                        <span>Subtotal</span>
                        <span>$ {{ number_format((float) $factura->subtotal, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between gap-4 text-neutral-400">
                        <span>IVA (19%)</span>
                        <span>$ {{ number_format((float) $factura->iva, 2, ',', '.') }}</span>
                    </div>
                    <div
                        class="flex justify-between gap-4 border-t border-neutral-700 pt-3 text-base font-bold text-neutral-100">
                        <span>Total</span>
                        <span>$ {{ number_format((float) $factura->total, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </section>

        @if ($puedeAgregarDetalle)
            @if ($factura->estado === 'pendiente')
                <section class="rounded-2xl border border-blue-500/25 bg-neutral-900/80 p-5">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-neutral-100">Agregar producto a la factura</h3>
                        <p class="mt-1 text-sm text-neutral-400">Selecciona un producto activo y registra la cantidad. El
                            inventario se actualizará automáticamente.</p>
                    </div>
                    @if ($productos->isEmpty())
                        <p class="rounded-lg border border-amber-500/20 bg-amber-500/5 p-4 text-sm text-amber-200">No hay
                            productos activos disponibles para agregar a esta factura.</p>
                    @else
                        <form action="{{ route('detalle_facturas.store') }}" method="POST"
                            class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                            @csrf
                            <input type="hidden" name="id_factura" value="{{ $factura->id }}">
                            <div>
                                <label for="id_producto"
                                    class="mb-2 block text-sm font-medium text-neutral-300">Producto</label>
                                <select id="id_producto" name="id_producto" required data-product-select
                                    class="w-full rounded-lg border px-3 py-2">
                                    <option value="">Selecciona un producto</option>
                                    @foreach ($productos as $producto)
                                        <option value="{{ $producto->id }}" data-price="{{ $producto->precio }}"
                                            data-stock="{{ $producto->stock }}" @selected(old('id_producto') == $producto->id)>
                                            {{ $producto->nombre }} · Stock: {{ $producto->stock }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="cantidad"
                                    class="mb-2 block text-sm font-medium text-neutral-300">Cantidad</label>
                                <input id="cantidad" type="number" name="cantidad" min="1" required
                                    value="{{ old('cantidad', 1) }}" class="w-full rounded-lg border px-3 py-2">
                            </div>
                            <div>
                                <label for="precio_unitario"
                                    class="mb-2 block text-sm font-medium text-neutral-300">Precio unitario</label>
                                <input id="precio_unitario" type="number" name="precio_unitario" min="0"
                                    step="0.01" required value="{{ old('precio_unitario') }}"
                                    class="w-full rounded-lg border px-3 py-2">
                            </div>
                            <div class="flex items-end">
                                <button type="submit"
                                    class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">Agregar
                                    producto</button>
                            </div>
                        </form>
                        <p class="mt-3 text-xs text-neutral-500">El precio sugerido corresponde al precio actual del
                            producto; puedes ajustarlo antes de guardar.</p>
                    @endif
                </section>
            @endif
        @endif

        @can('eliminar facturas')
            @if ($factura->estado !== 'pendiente')
                <p class="text-sm text-neutral-500">Esta factura no se puede eliminar porque su estado es
                    {{ $factura->estado }}. El historial se conserva para mantener la trazabilidad comercial.</p>
            @endif
        @endcan
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-product-select]').forEach(function(select) {
            const form = select.closest('form');
            const priceInput = form ? form.querySelector('[name="precio_unitario"]') : null;

            if (!priceInput) return;

            select.addEventListener('change', function() {
                const option = select.options[select.selectedIndex];
                if (option && option.dataset.price) {
                    priceInput.value = option.dataset.price;
                }
            });

            if (!priceInput.value && select.value) {
                const option = select.options[select.selectedIndex];
                if (option && option.dataset.price) {
                    priceInput.value = option.dataset.price;
                }
            }
        });
    </script>
@endpush
