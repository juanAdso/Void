@extends('layouts.app')

@section('title', 'Factura #' . $factura->id)
@section('page-title', 'Factura #' . $factura->id)

@section('content')
    <div class="mx-auto max-w-7xl space-y-6">
        <header class="flex flex-col gap-4 border-b border-neutral-800 pb-5 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="void-page-eyebrow">Facturación / Detalle de factura</p>
                <div class="mt-2 flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl font-bold text-neutral-50 sm:text-3xl">Factura #{{ $factura->id }}</h1>
                    @if ($factura->estado === 'pagada')
                        <span class="rounded-full bg-green-500/15 px-3 py-1 text-xs font-semibold text-green-300">Pagada</span>
                    @elseif ($factura->estado === 'pendiente')
                        <span class="rounded-full bg-amber-500/15 px-3 py-1 text-xs font-semibold text-amber-300">Pendiente</span>
                    @else
                        <span class="rounded-full bg-red-500/15 px-3 py-1 text-xs font-semibold text-red-300">Anulada</span>
                    @endif
                </div>
                <p class="mt-2 text-sm text-neutral-400">Consulta y gestiona los datos generales y los productos de esta factura desde una sola pantalla.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('facturas.index') }}"
                    class="rounded-lg border border-neutral-700 px-4 py-2 text-sm font-semibold text-neutral-200 transition hover:bg-neutral-800">
                    Volver al listado
                </a>
                @can('eliminar facturas')
                    @if ($factura->estado === 'pendiente')
                        <form action="{{ route('facturas.destroy', $factura->id) }}" method="POST"
                            onsubmit="return confirm('¿Eliminar la factura #{{ $factura->id }}? Se eliminarán sus productos asociados, se devolverán las unidades al inventario y se registrarán los movimientos correspondientes.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700">
                                Eliminar factura
                            </button>
                        </form>
                    @endif
                @endcan
            </div>
        </header>

        @if ($errors->any())
            <div class="rounded-xl border border-red-500/40 bg-red-950/30 p-4 text-sm text-red-200" role="alert">
                <p class="mb-2 font-semibold">No se pudieron guardar los cambios:</p>
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="overflow-hidden rounded-2xl border border-neutral-800 bg-neutral-900/80">
            <div class="border-b border-neutral-800 px-5 py-4">
                <h2 class="text-lg font-semibold text-neutral-100">Datos generales de la factura</h2>
                <p class="mt-1 text-sm text-neutral-400">Cliente, fecha de emisión y estado del comprobante.</p>
            </div>

            @if ($puedeEditarFactura)
                <form action="{{ route('facturas.update', $factura->id) }}" method="POST" class="space-y-5 p-5">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <div>
                            <label for="id_cliente" class="mb-2 block text-sm font-medium text-neutral-300">Cliente</label>
                            @if ($factura->estado === 'pagada')
                                <input type="text" disabled
                                    value="{{ trim(($factura->cliente?->nombre ?? '') . ' ' . ($factura->cliente?->apellido ?? '')) }}"
                                    class="w-full rounded-lg border px-3 py-2.5 text-sm">
                                <input type="hidden" name="id_cliente" value="{{ $factura->id_cliente }}">
                            @else
                                <select id="id_cliente" name="id_cliente" required class="w-full rounded-lg border px-3 py-2.5 text-sm">
                                    <option value="">Selecciona un cliente</option>
                                    @foreach ($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" @selected(old('id_cliente', $factura->id_cliente) == $cliente->id)>
                                            {{ $cliente->nombre }} {{ $cliente->apellido }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div>
                            <label for="fecha" class="mb-2 block text-sm font-medium text-neutral-300">Fecha de emisión</label>
                            @if ($factura->estado === 'pagada')
                                <input type="text" disabled value="{{ $factura->fecha?->format('d/m/Y') }}"
                                    class="w-full rounded-lg border px-3 py-2.5 text-sm">
                                <input type="hidden" name="fecha" value="{{ $factura->fecha?->format('Y-m-d') }}">
                            @else
                                <input id="fecha" type="date" name="fecha" required
                                    value="{{ old('fecha', $factura->fecha?->format('Y-m-d')) }}"
                                    class="w-full rounded-lg border px-3 py-2.5 text-sm">
                            @endif
                        </div>
                        <div>
                            <label for="estado" class="mb-2 block text-sm font-medium text-neutral-300">Estado</label>
                            <select id="estado" name="estado" required class="w-full rounded-lg border px-3 py-2.5 text-sm">
                                @if ($factura->estado === 'pagada')
                                    <option value="pagada" selected>Pagada</option>
                                    <option value="anulada">Anulada</option>
                                @else
                                    <option value="pendiente" selected>Pendiente</option>
                                    <option value="pagada">Pagada</option>
                                    <option value="anulada">Anulada</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    @if ($factura->estado === 'pagada')
                        <p class="rounded-lg border border-amber-500/25 bg-amber-500/5 px-4 py-3 text-sm text-amber-200">
                            Una factura pagada conserva su cliente, fecha y detalles. Solo puede mantenerse pagada o anularse.
                        </p>
                    @else
                        <p class="rounded-lg border border-blue-500/25 bg-blue-500/5 px-4 py-3 text-sm text-blue-200">
                            El subtotal, el IVA y el total se calculan automáticamente a partir de los detalles; no se modifican manualmente.
                        </p>
                    @endif

                    <div class="flex justify-end border-t border-neutral-800 pt-4">
                        <button type="submit"
                            class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                            Guardar datos de factura
                        </button>
                    </div>
                </form>
            @else
                <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Cliente</p>
                        <p class="mt-1 font-medium text-neutral-100">
                            {{ trim(($factura->cliente?->nombre ?? '') . ' ' . ($factura->cliente?->apellido ?? '')) ?: 'Cliente no disponible' }}
                        </p>
                        <p class="mt-1 text-xs text-neutral-500">{{ $factura->cliente?->documento ?? 'Sin documento' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Fecha</p>
                        <p class="mt-1 font-medium text-neutral-100">{{ $factura->fecha?->format('d/m/Y') ?? 'Sin fecha' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Responsable</p>
                        <p class="mt-1 font-medium text-neutral-100">
                            {{ trim(($factura->usuario?->nombre ?? '') . ' ' . ($factura->usuario?->apellido ?? '')) ?: 'Usuario no disponible' }}
                        </p>
                    </div>
                </div>
                @if ($factura->estado === 'anulada')
                    <p class="border-t border-neutral-800 px-5 py-3 text-sm text-neutral-400">
                        Esta factura está anulada y se conserva como historial. Sus datos y detalles son de solo lectura.
                    </p>
                @endif
            @endif
        </section>

        <section class="overflow-hidden rounded-2xl border border-neutral-800 bg-neutral-900/80">
            <div class="flex flex-col gap-2 border-b border-neutral-800 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-neutral-100">Productos y detalles de factura</h2>
                    <p class="mt-1 text-sm text-neutral-400">Edita producto, cantidad y precio unitario directamente en cada renglón.</p>
                </div>
                <span class="rounded-full bg-neutral-800 px-3 py-1 text-xs font-medium text-neutral-300">
                    {{ $factura->detalle_factura->count() }} detalle(s)
                </span>
            </div>

            @if ($factura->estado === 'pagada')
                <p class="border-b border-amber-500/20 bg-amber-500/5 px-5 py-3 text-sm text-amber-200">
                    Los detalles de una factura pagada son de solo lectura. Puedes anularla desde los datos generales cuando corresponda.
                </p>
            @elseif ($factura->estado === 'pendiente' && !$puedeEditarDetalle && !$puedeEliminarDetalle && !$puedeAgregarDetalle)
                <p class="border-b border-neutral-800 px-5 py-3 text-sm text-neutral-400">
                    Tu cuenta no tiene permisos para modificar los productos de esta factura.
                </p>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] border-collapse" data-no-column-filters="true">
                    <thead>
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide">Producto</th>
                            <th class="w-28 px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide">Cantidad</th>
                            <th class="w-40 px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide">Precio unitario</th>
                            <th class="w-36 px-3 py-3 text-right text-xs font-semibold uppercase tracking-wide">Subtotal</th>
                            @if ($mostrarAccionesDetalle)
                                <th class="w-40 px-3 py-3 text-center text-xs font-semibold uppercase tracking-wide">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($factura->detalle_factura as $detalle)
                            @php($formDetalle = 'editar-detalle-' . $detalle->id)
                            <tr class="border-t border-neutral-800 align-top">
                                <td class="px-3 py-3">
                                    @if ($puedeEditarDetalle)
                                        <select name="id_producto" form="{{ $formDetalle }}" data-product-select required
                                            class="w-full min-w-0 rounded-lg border px-2 py-2 text-sm">
                                            @if (!$productos->contains('id', $detalle->id_producto))
                                                <option value="{{ $detalle->id_producto }}" selected disabled>
                                                    {{ $detalle->producto?->nombre ?? 'Producto inactivo' }} (no disponible)
                                                </option>
                                            @endif
                                            @foreach ($productos as $producto)
                                                <option value="{{ $producto->id }}" data-price="{{ $producto->precio }}"
                                                    @selected($detalle->id_producto == $producto->id)>
                                                    {{ $producto->nombre }} · Stock: {{ $producto->stock }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <span class="font-medium text-neutral-100">
                                            {{ $detalle->producto?->nombre ?? 'Producto no disponible' }}
                                        </span>
                                    @endif
                                    <p class="mt-1 text-xs text-neutral-500">Detalle #{{ $detalle->id }}</p>
                                </td>
                                <td class="px-3 py-3">
                                    @if ($puedeEditarDetalle)
                                        <input type="number" name="cantidad" form="{{ $formDetalle }}" min="1" required
                                            value="{{ $detalle->cantidad }}" class="w-full min-w-0 rounded-lg border px-2 py-2 text-sm">
                                    @else
                                        <span>{{ $detalle->cantidad }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3">
                                    @if ($puedeEditarDetalle)
                                        <input type="number" name="precio_unitario" form="{{ $formDetalle }}" min="0"
                                            step="0.01" required value="{{ $detalle->precio_unitario }}"
                                            class="w-full min-w-0 rounded-lg border px-2 py-2 text-sm">
                                    @else
                                        <span>$ {{ number_format((float) $detalle->precio_unitario, 2, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-right font-semibold text-neutral-100">
                                    $ <span data-line-subtotal>{{ number_format((float) $detalle->subtotal, 2, ',', '.') }}</span>
                                </td>
                                @if ($mostrarAccionesDetalle)
                                    <td class="px-3 py-3">
                                        <div class="flex flex-wrap items-center justify-center gap-2">
                                            @if ($puedeEditarDetalle)
                                                <form id="{{ $formDetalle }}" action="{{ route('detalle_facturas.update', $detalle->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="id_factura" value="{{ $factura->id }}">
                                                </form>
                                                <button type="submit" form="{{ $formDetalle }}"
                                                    class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                                                    Guardar
                                                </button>
                                            @endif
                                            @if ($puedeEliminarDetalle)
                                                <form action="{{ route('detalle_facturas.destroy', $detalle->id) }}" method="POST"
                                                    onsubmit="return confirm('¿Retirar este producto? Las existencias se devolverán al inventario.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="rounded-lg border border-red-500/40 px-3 py-2 text-xs font-semibold text-red-300 transition hover:bg-red-500/10">
                                                        Retirar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $mostrarAccionesDetalle ? 5 : 4 }}"
                                    class="px-4 py-10 text-center text-sm text-neutral-400">
                                    Esta factura todavía no tiene productos asociados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-neutral-800 bg-neutral-950/40 px-5 py-4">
                <div class="ml-auto grid max-w-md grid-cols-3 gap-3 text-sm">
                    <div>
                        <p class="text-xs text-neutral-400">Subtotal</p>
                        <p class="mt-1 font-semibold text-neutral-100">$ {{ number_format((float) $factura->subtotal, 2, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-400">IVA (19 %)</p>
                        <p class="mt-1 font-semibold text-neutral-100">$ {{ number_format((float) $factura->iva, 2, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-400">Total</p>
                        <p class="mt-1 font-bold text-blue-300">$ {{ number_format((float) $factura->total, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </section>

        @if ($puedeAgregarDetalle)
            <section class="rounded-2xl border border-blue-500/25 bg-neutral-900/80 p-5">
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-neutral-100">Agregar producto a la factura</h2>
                    <p class="mt-1 text-sm text-neutral-400">El inventario y los totales se actualizarán al guardar.</p>
                </div>
                @if ($productos->isEmpty())
                    <p class="rounded-lg border border-amber-500/25 bg-amber-500/5 px-4 py-3 text-sm text-amber-200">
                        No hay productos activos disponibles para agregar.
                    </p>
                @else
                    <form action="{{ route('detalle_facturas.store') }}" method="POST"
                        class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @csrf
                        <input type="hidden" name="id_factura" value="{{ $factura->id }}">
                        <div>
                            <label for="id_producto_nuevo" class="mb-2 block text-sm font-medium text-neutral-300">Producto</label>
                            <select id="id_producto_nuevo" name="id_producto" required data-product-select
                                class="w-full rounded-lg border px-3 py-2.5 text-sm">
                                <option value="">Selecciona un producto</option>
                                @foreach ($productos as $producto)
                                    <option value="{{ $producto->id }}" data-price="{{ $producto->precio }}"
                                        data-stock="{{ $producto->stock }}">
                                        {{ $producto->nombre }} · Stock: {{ $producto->stock }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="cantidad_nueva" class="mb-2 block text-sm font-medium text-neutral-300">Cantidad</label>
                            <input id="cantidad_nueva" type="number" name="cantidad" min="1" required value="1"
                                class="w-full rounded-lg border px-3 py-2.5 text-sm">
                        </div>
                        <div>
                            <label for="precio_unitario_nuevo" class="mb-2 block text-sm font-medium text-neutral-300">Precio unitario</label>
                            <input id="precio_unitario_nuevo" type="number" name="precio_unitario" min="0" step="0.01"
                                required class="w-full rounded-lg border px-3 py-2.5 text-sm">
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                                Agregar producto
                            </button>
                        </div>
                    </form>
                @endif
            </section>
        @endif

        @can('eliminar facturas')
            @if ($factura->estado !== 'pendiente')
                <p class="text-xs text-neutral-500">Las facturas pagadas o anuladas se conservan como parte del historial comercial.</p>
            @endif
        @endcan
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-product-select]').forEach(function(select) {
            const form = select.form || select.closest('tr')?.querySelector('form');
            const priceInput = form ? form.querySelector('[name="precio_unitario"]') : null;

            if (!priceInput) {
                const row = select.closest('tr');
                const linkedForm = row ? document.getElementById(row.querySelector('[name="cantidad"]')?.getAttribute('form')) : null;
                const linkedPrice = row ? row.querySelector('[name="precio_unitario"]') : null;
                if (linkedPrice) {
                    select.addEventListener('change', function() {
                        const option = select.options[select.selectedIndex];
                        if (option?.dataset.price) linkedPrice.value = option.dataset.price;
                        actualizarSubtotal(row);
                    });
                }
                return;
            }

            select.addEventListener('change', function() {
                const option = select.options[select.selectedIndex];
                if (option?.dataset.price) priceInput.value = option.dataset.price;
            });
        });

        function actualizarSubtotal(row) {
            const cantidad = row.querySelector('[name="cantidad"]');
            const precio = row.querySelector('[name="precio_unitario"]');
            const subtotal = row.querySelector('[data-line-subtotal]');

            if (!cantidad || !precio || !subtotal) return;

            const valor = (Number(cantidad.value) || 0) * (Number(precio.value) || 0);
            subtotal.textContent = new Intl.NumberFormat('es-CO', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(valor);
        }

        document.querySelectorAll('tr [name="cantidad"], tr [name="precio_unitario"]').forEach((input) => {
            input.addEventListener('input', () => actualizarSubtotal(input.closest('tr')));
        });
    </script>
@endpush
