@extends('layouts.app')

@section('title', 'Crear factura')
@section('page-title', 'Nueva factura')

@section('content')
    <div class="mx-auto max-w-7xl space-y-6">
        <header class="flex flex-col gap-3 border-b border-neutral-800 pb-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-amber-400">Facturación / Nuevo registro</p>
                <h1 class="mt-2 text-2xl font-bold text-neutral-50 sm:text-3xl">Crear factura</h1>
                <p class="mt-2 max-w-2xl text-sm text-neutral-400">
                    Registra los datos del cliente y agrega todos los productos de la venta en este mismo formulario.
                </p>
            </div>
            <a href="{{ route('facturas.index') }}"
                class="inline-flex items-center justify-center rounded-lg border border-neutral-700 px-4 py-2 text-sm font-semibold text-neutral-200 transition hover:bg-neutral-800">
                Volver a facturas
            </a>
        </header>

        @if ($errors->any())
            <div class="rounded-xl border border-red-500/40 bg-red-950/30 p-4 text-sm text-red-200" role="alert">
                <p class="mb-2 font-semibold">Revisa la información del formulario:</p>
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="crear-factura-form" action="{{ route('facturas.store') }}" method="POST" class="space-y-6">
            @csrf

            <section class="overflow-hidden rounded-2xl border border-neutral-800 bg-neutral-900/80">
                <div class="border-b border-neutral-800 px-5 py-4">
                    <h2 class="text-lg font-semibold text-neutral-100">Datos generales</h2>
                    <p class="mt-1 text-sm text-neutral-400">Información principal del comprobante.</p>
                </div>
                <div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-2 xl:grid-cols-3">
                    <div>
                        <label for="id_cliente" class="mb-2 block text-sm font-medium text-neutral-300">Cliente</label>
                        <select id="id_cliente" name="id_cliente" required class="w-full rounded-lg border px-3 py-2.5 text-sm">
                            <option value="">Selecciona un cliente</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}" @selected(old('id_cliente') == $cliente->id)>
                                    {{ $cliente->nombre }} {{ $cliente->apellido }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="fecha" class="mb-2 block text-sm font-medium text-neutral-300">Fecha de emisión</label>
                        <input id="fecha" type="date" name="fecha" value="{{ old('fecha', now()->toDateString()) }}"
                            required class="w-full rounded-lg border px-3 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-neutral-300">Estado inicial</label>
                        <input type="text" value="Pendiente" disabled
                            class="w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2.5 text-sm text-amber-300">
                        <input type="hidden" name="estado" value="pendiente">
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-neutral-800 bg-neutral-900/80">
                <div class="flex flex-col gap-3 border-b border-neutral-800 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-neutral-100">Productos y detalles</h2>
                        <p class="mt-1 text-sm text-neutral-400">Puedes agregar varios productos y ajustar su cantidad y precio unitario.</p>
                    </div>
                    <button type="button" id="agregar-detalle"
                        class="inline-flex items-center justify-center rounded-lg border border-blue-500/40 bg-blue-500/10 px-4 py-2 text-sm font-semibold text-blue-200 transition hover:bg-blue-500/20">
                        Agregar producto
                    </button>
                </div>

                <div class="space-y-3 p-5" id="detalles-container">
                    <p id="sin-detalles" class="rounded-xl border border-dashed border-neutral-700 px-4 py-8 text-center text-sm text-neutral-400">
                        Aún no has agregado productos. Usa «Agregar producto» para incluir el primer detalle.
                    </p>
                </div>

                <template id="detalle-template">
                    <div class="grid grid-cols-1 items-end gap-3 rounded-xl border border-neutral-800 bg-neutral-950/50 p-4 md:grid-cols-2 xl:grid-cols-[minmax(220px,2fr)_minmax(100px,0.7fr)_minmax(140px,1fr)_minmax(130px,1fr)_auto]" data-detalle-row>
                        <div>
                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-neutral-400">Producto</label>
                            <select name="detalles[__INDEX__][id_producto]" required data-product-select
                                class="w-full rounded-lg border px-3 py-2.5 text-sm">
                                <option value="">Selecciona un producto</option>
                                @foreach ($productos as $producto)
                                    <option value="{{ $producto->id }}" data-price="{{ $producto->precio }}" data-stock="{{ $producto->stock }}">
                                        {{ $producto->nombre }} · Stock: {{ $producto->stock }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-neutral-400">Cantidad</label>
                            <input type="number" name="detalles[__INDEX__][cantidad]" min="1" step="1" value="1" required data-quantity
                                class="w-full rounded-lg border px-3 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-neutral-400">Precio unitario</label>
                            <input type="number" name="detalles[__INDEX__][precio_unitario]" min="0" step="0.01" required data-price-input
                                class="w-full rounded-lg border px-3 py-2.5 text-sm" placeholder="0.00">
                        </div>
                        <div class="pb-2 text-sm">
                            <span class="block text-xs text-neutral-500">Subtotal del detalle</span>
                            <strong data-line-total class="mt-1 block text-neutral-100">$ 0,00</strong>
                        </div>
                        <button type="button" data-remove-detail
                            class="rounded-lg border border-red-500/30 px-3 py-2.5 text-xs font-semibold text-red-300 transition hover:bg-red-500/10">
                            Quitar
                        </button>
                    </div>
                </template>

                <div class="border-t border-neutral-800 bg-neutral-950/40 px-5 py-4">
                    <div class="ml-auto grid max-w-md grid-cols-3 gap-3 text-sm">
                        <div>
                            <p class="text-xs text-neutral-400">Subtotal</p>
                            <p id="subtotal-estimado" class="mt-1 font-semibold text-neutral-100">$ 0,00</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-400">IVA (19 %)</p>
                            <p id="iva-estimado" class="mt-1 font-semibold text-neutral-100">$ 0,00</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-400">Total</p>
                            <p id="total-estimado" class="mt-1 font-bold text-blue-300">$ 0,00</p>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-neutral-500">
                        Los valores son una vista previa. VOID valida las existencias y vuelve a calcular los totales al guardar.
                    </p>
                </div>
            </section>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('facturas.index') }}"
                    class="rounded-lg border border-neutral-700 px-5 py-2.5 text-center text-sm font-semibold text-neutral-200 transition hover:bg-neutral-800">
                    Cancelar
                </a>
                <button type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Crear factura
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const container = document.getElementById('detalles-container');
            const template = document.getElementById('detalle-template');
            const addButton = document.getElementById('agregar-detalle');
            const form = document.getElementById('crear-factura-form');
            const emptyMessage = document.getElementById('sin-detalles');
            let nextIndex = 0;

            const formatoMoneda = (valor) => new Intl.NumberFormat('es-CO', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(valor);

            function recalcularTotales() {
                let subtotal = 0;

                container.querySelectorAll('[data-detalle-row]').forEach((row) => {
                    const cantidad = Number(row.querySelector('[data-quantity]').value) || 0;
                    const precio = Number(row.querySelector('[data-price-input]').value) || 0;
                    const parcial = cantidad * precio;

                    subtotal += parcial;
                    row.querySelector('[data-line-total]').textContent = '$ ' + formatoMoneda(parcial);
                });

                const iva = subtotal * 0.19;
                document.getElementById('subtotal-estimado').textContent = '$ ' + formatoMoneda(subtotal);
                document.getElementById('iva-estimado').textContent = '$ ' + formatoMoneda(iva);
                document.getElementById('total-estimado').textContent = '$ ' + formatoMoneda(subtotal + iva);
                emptyMessage.classList.toggle('hidden', container.querySelectorAll('[data-detalle-row]').length > 0);
            }

            function agregarDetalle() {
                const fragmento = template.content.cloneNode(true);
                const row = fragmento.querySelector('[data-detalle-row]');

                row.querySelectorAll('[name]').forEach((input) => {
                    input.name = input.name.replaceAll('__INDEX__', String(nextIndex));
                });

                nextIndex += 1;
                row.querySelector('[data-remove-detail]').addEventListener('click', () => {
                    row.remove();
                    recalcularTotales();
                });
                row.querySelector('[data-product-select]').addEventListener('change', (event) => {
                    const option = event.target.options[event.target.selectedIndex];
                    const price = row.querySelector('[data-price-input]');

                    if (option.dataset.price) {
                        price.value = option.dataset.price;
                    }

                    recalcularTotales();
                });
                row.querySelectorAll('[data-quantity], [data-price-input]').forEach((input) => {
                    input.addEventListener('input', recalcularTotales);
                });

                container.appendChild(fragmento);
                recalcularTotales();
            }

            addButton.addEventListener('click', agregarDetalle);
            form.addEventListener('submit', () => {
                container.querySelectorAll('[data-detalle-row]').forEach((row) => {
                    if (!row.querySelector('[data-product-select]').value) {
                        row.remove();
                    }
                });
            });

            agregarDetalle();
        })();
    </script>
@endpush
