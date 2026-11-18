@extends('layouts.app')

@section('title', 'Editar Factura')
@section('page-title', 'Editar factura')

@section('content')
    <div class="mx-auto max-w-7xl space-y-6">
        <header class="flex flex-col gap-4 border-b border-neutral-800 pb-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-amber-400">Facturación / Edición</p>
                <div class="mt-2 flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl font-bold text-white sm:text-3xl">Editar factura #{{ $facturas->id }}</h1>
                    @if ($facturas->estado === 'pagada')
                        <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-300">Pagada</span>
                    @else
                        <span class="rounded-full bg-amber-500/15 px-3 py-1 text-xs font-semibold text-amber-300">Pendiente</span>
                    @endif
                </div>
                <p class="mt-2 max-w-2xl text-sm text-neutral-400">Edita los datos de la factura y administra sus productos desde esta misma pantalla.</p>
            </div>
            <a href="{{ route('facturas.show', $facturas->id) }}"
                class="inline-flex items-center justify-center rounded-lg border border-neutral-700 px-4 py-2 text-sm font-semibold text-neutral-200 transition hover:border-blue-400 hover:text-blue-300">
                Volver a consultar
            </a>
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
                <h2 class="text-lg font-semibold text-white">Datos generales</h2>
                <p class="mt-1 text-sm text-neutral-400">Información principal y estado de la factura.</p>
            </div>

            <form action="{{ route('facturas.update', $facturas->id) }}" method="POST" class="space-y-5 p-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <div>
                        <label for="id_cliente" class="mb-2 block text-sm font-medium text-neutral-300">Cliente</label>
                        @if ($facturas->estado === 'pagada')
                            <select id="id_cliente" disabled class="w-full rounded-lg border px-3 py-2.5 text-sm">
                                <option>{{ $facturas->cliente?->nombre }} {{ $facturas->cliente?->apellido }}</option>
                            </select>
                            <input type="hidden" name="id_cliente" value="{{ $facturas->id_cliente }}">
                        @else
                            <select id="id_cliente" name="id_cliente" required class="w-full rounded-lg border px-3 py-2.5 text-sm">
                                <option value="">Selecciona un cliente</option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" @selected(old('id_cliente', $facturas->id_cliente) == $cliente->id)>
                                        {{ $cliente->nombre }} {{ $cliente->apellido }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div>
                        <label for="fecha" class="mb-2 block text-sm font-medium text-neutral-300">Fecha de emisión</label>
                        <input id="fecha" type="date" name="fecha" required
                            value="{{ old('fecha', $facturas->fecha?->format('Y-m-d')) }}"
                            @disabled($facturas->estado === 'pagada')
                            class="w-full rounded-lg border px-3 py-2.5 text-sm">
                        @if ($facturas->estado === 'pagada')
                            <input type="hidden" name="fecha" value="{{ $facturas->fecha?->format('Y-m-d') }}">
                        @endif
                    </div>

                    <div>
                        <label for="estado" class="mb-2 block text-sm font-medium text-neutral-300">Estado</label>
                        <select id="estado" name="estado" required class="w-full rounded-lg border px-3 py-2.5 text-sm">
                            @if ($facturas->estado === 'pagada')
                                <option value="pagada" @selected(old('estado', $facturas->estado) === 'pagada')>Pagada</option>
                                <option value="anulada" @selected(old('estado') === 'anulada')>Anulada</option>
                            @else
                                <option value="pendiente" @selected(old('estado', $facturas->estado) === 'pendiente')>Pendiente</option>
                                <option value="pagada" @selected(old('estado') === 'pagada')>Pagada</option>
                                <option value="anulada" @selected(old('estado') === 'anulada')>Anulada</option>
                            @endif
                        </select>
                    </div>
                </div>

                @if ($facturas->estado === 'pagada')
                    <p class="rounded-lg border border-amber-500/25 bg-amber-500/5 px-4 py-3 text-sm text-amber-200">
                        Una factura pagada conserva su cliente, fecha y productos. Puedes mantenerla pagada o anularla; al anularla, VOID devuelve las existencias al inventario.
                    </p>
                @else
                    <p class="rounded-lg border border-blue-500/25 bg-blue-500/5 px-4 py-3 text-sm text-blue-200">
                        El subtotal, el IVA (19 %) y el total se calculan automáticamente con los productos, cantidades y precios. No se editan manualmente para mantener los valores correctos.
                    </p>
                @endif

                <div class="flex flex-col gap-3 border-t border-neutral-800 pt-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="grid grid-cols-3 gap-3 text-sm">
                        <div>
                            <p class="text-xs text-neutral-400">Subtotal</p>
                            <p class="mt-1 font-semibold text-neutral-100">$ {{ number_format((float) $facturas->subtotal, 2, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-400">IVA (19 %)</p>
                            <p class="mt-1 font-semibold text-neutral-100">$ {{ number_format((float) $facturas->iva, 2, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-400">Total</p>
                            <p class="mt-1 font-bold text-blue-300">$ {{ number_format((float) $facturas->total, 2, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                            Guardar datos de factura
                        </button>
                        <a href="{{ route('facturas.show', $facturas->id) }}"
                            class="rounded-lg border border-neutral-700 px-5 py-2.5 text-sm font-semibold text-neutral-200 transition hover:bg-neutral-800">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-2xl border border-neutral-800 bg-neutral-900/80">
            <div class="flex flex-col gap-2 border-b border-neutral-800 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-white">Productos y detalles de factura</h2>
                    <p class="mt-1 text-sm text-neutral-400">Modifica el producto, la cantidad y el precio unitario de cada registro.</p>
                </div>
                <span class="rounded-full bg-neutral-800 px-3 py-1 text-xs font-medium text-neutral-300">
                    {{ $facturas->detalle_factura->count() }} detalle(s)
                </span>
            </div>

            @if ($facturas->estado === 'pagada')
                <div class="border-b border-amber-500/20 bg-amber-500/5 px-5 py-3 text-sm text-amber-200">
                    Los detalles de una factura pagada son de solo lectura. Anula la factura únicamente cuando corresponda; el inventario se restituirá automáticamente.
                </div>
            @elseif (!$puedeEditarDetalle && !$puedeEliminarDetalle && !$puedeAgregarDetalle)
                <div class="border-b border-neutral-800 px-5 py-3 text-sm text-neutral-400">
                    Tu cuenta puede editar los datos generales, pero no tiene permisos para modificar los productos de la factura.
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full border-collapse" data-no-column-filters="true">
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
                        @forelse ($facturas->detalle_factura as $detalle)
                            @php($formDetalle = 'editar-detalle-' . $detalle->id)
                            <tr class="border-t border-neutral-800">
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
                                                <option value="{{ $producto->id }}" data-price="{{ $producto->precio }}" @selected($detalle->id_producto == $producto->id)>
                                                    {{ $producto->nombre }} · Stock: {{ $producto->stock }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <span class="font-medium text-neutral-100">{{ $detalle->producto?->nombre ?? 'Producto no disponible' }}</span>
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
                                        <input type="number" name="precio_unitario" form="{{ $formDetalle }}" min="0" step="0.01" required
                                            value="{{ $detalle->precio_unitario }}" class="w-full min-w-0 rounded-lg border px-2 py-2 text-sm">
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
                                                    <input type="hidden" name="id_factura" value="{{ $facturas->id }}">
                                                    <input type="hidden" name="volver_edicion" value="1">
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
                                                    <input type="hidden" name="volver_edicion" value="1">
                                                    <button type="submit" class="rounded-lg border border-red-500/40 px-3 py-2 text-xs font-semibold text-red-300 transition hover:bg-red-500/10">
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
                                <td colspan="{{ $mostrarAccionesDetalle ? 5 : 4 }}" class="px-4 py-10 text-center text-sm text-neutral-400">
                                    Esta factura todavía no tiene productos asociados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-neutral-800 bg-neutral-950/40 px-5 py-4">
                <div class="ml-auto grid max-w-md grid-cols-2 gap-2 text-sm sm:grid-cols-3">
                    <div>
                        <p class="text-xs text-neutral-400">Subtotal</p>
                        <p class="mt-1 font-semibold text-neutral-100">$ {{ number_format((float) $facturas->subtotal, 2, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-neutral-400">IVA (19 %)</p>
                        <p class="mt-1 font-semibold text-neutral-100">$ {{ number_format((float) $facturas->iva, 2, ',', '.') }}</p>
                    </div>
                    <div class="col-span-2 border-t border-neutral-800 pt-2 sm:col-span-1 sm:border-0 sm:pt-0">
                        <p class="text-xs text-neutral-400">Total factura</p>
                        <p class="mt-1 text-base font-bold text-blue-300">$ {{ number_format((float) $facturas->total, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </section>

        @if ($puedeAgregarDetalle)
            <section class="rounded-2xl border border-blue-500/25 bg-neutral-900/80 p-5">
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-white">Agregar producto a la factura</h2>
                    <p class="mt-1 text-sm text-neutral-400">Elige un producto activo. VOID validará las existencias y actualizará los totales automáticamente.</p>
                </div>
                @if ($productos->isEmpty())
                    <p class="rounded-lg border border-amber-500/25 bg-amber-500/5 px-4 py-3 text-sm text-amber-200">
                        No hay productos activos disponibles para agregar.
                    </p>
                @else
                    <form action="{{ route('detalle_facturas.store') }}" method="POST" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @csrf
                        <input type="hidden" name="id_factura" value="{{ $facturas->id }}">
                        <input type="hidden" name="volver_edicion" value="1">
                        <div>
                            <label for="id_producto_nuevo" class="mb-2 block text-sm font-medium text-neutral-300">Producto</label>
                            <select id="id_producto_nuevo" name="id_producto" required data-product-select
                                class="w-full rounded-lg border px-3 py-2.5 text-sm">
                                <option value="">Selecciona un producto</option>
                                @foreach ($productos as $producto)
                                    <option value="{{ $producto->id }}" data-price="{{ $producto->precio }}"
                                        @selected(old('id_producto') == $producto->id)>
                                        {{ $producto->nombre }} · Stock: {{ $producto->stock }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="cantidad_nueva" class="mb-2 block text-sm font-medium text-neutral-300">Cantidad</label>
                            <input id="cantidad_nueva" type="number" name="cantidad" min="1" required value="{{ old('cantidad', 1) }}"
                                class="w-full rounded-lg border px-3 py-2.5 text-sm">
                        </div>
                        <div>
                            <label for="precio_unitario_nuevo" class="mb-2 block text-sm font-medium text-neutral-300">Precio unitario</label>
                            <input id="precio_unitario_nuevo" type="number" name="precio_unitario" min="0" step="0.01"
                                required value="{{ old('precio_unitario') }}" class="w-full rounded-lg border px-3 py-2.5 text-sm">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                                Agregar producto
                            </button>
                        </div>
                    </form>
                @endif
            </section>
        @endif

        <p class="text-xs text-neutral-500">
            El identificador de la factura se mantiene fijo en cada detalle y los subtotales, el IVA y el total son calculados por VOID. Los cambios de cantidades actualizan el inventario y dejan su movimiento registrado.
        </p>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-product-select]').forEach(function(select) {
            const form = select.form;
            const priceInput = form ? form.elements.namedItem('precio_unitario') : null;

            if (!priceInput) {
                return;
            }

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

        document.querySelectorAll('[data-line-subtotal]').forEach(function(subtotal) {
            const row = subtotal.closest('tr');
            const quantity = row ? row.querySelector('[name="cantidad"]') : null;
            const price = row ? row.querySelector('[name="precio_unitario"]') : null;

            if (!quantity || !price) {
                return;
            }

            const updateSubtotal = function() {
                const value = (Number(quantity.value) || 0) * (Number(price.value) || 0);
                subtotal.textContent = new Intl.NumberFormat('es-CO', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(value);
            };

            quantity.addEventListener('input', updateSubtotal);
            price.addEventListener('input', updateSubtotal);
        });
    </script>
@endpush
