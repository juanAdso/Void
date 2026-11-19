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
                        <span
                            class="rounded-full bg-green-500/15 px-3 py-1 text-xs font-semibold text-green-300">Pagada</span>
                    @elseif ($factura->estado === 'pendiente')
                        <span
                            class="rounded-full bg-amber-500/15 px-3 py-1 text-xs font-semibold text-amber-300">Pendiente</span>
                    @else
                        <span class="rounded-full bg-red-500/15 px-3 py-1 text-xs font-semibold text-red-300">Anulada</span>
                    @endif
                </div>
                <p class="mt-2 text-sm text-neutral-400">Consulta y gestiona los datos generales y los productos de esta
                    factura desde una sola pantalla.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('facturas.index') }}"
                    class="rounded-lg border border-neutral-700 px-4 py-2 text-sm font-semibold text-neutral-200 transition hover:bg-neutral-800">
                    Volver al listado
                </a>
                @can('eliminar facturas')
                    @if ($factura->estado === 'pendiente')
                        <form action="{{ route('facturas.destroy', $factura->id) }}" method="POST"
                            onsubmit="return confirm('¿Eliminar la factura #{{ $factura->id }}? Se devolverán las unidades al inventario y se registrarán los movimientos correspondientes.');">
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

        @if (session('error'))
            <div class="rounded-xl border border-red-500/40 bg-red-950/30 p-4 text-sm text-red-200" role="alert">
                {{ session('error') }}</div>
        @endif
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

        @if ($puedeGuardarCambios)
            <form id="form-editar-factura" action="{{ route('facturas.guardar-cambios', $factura->id) }}" method="POST"
                class="space-y-6">
                @csrf
                @method('PUT')

                @if ($puedeEditarCliente)
                    @php
                        $clienteSeleccionado = $clientes->firstWhere(
                            'id',
                            (int) old('id_cliente', $factura->id_cliente),
                        );
                        $clienteEtiqueta = $clienteSeleccionado
                            ? trim($clienteSeleccionado->nombre . ' ' . $clienteSeleccionado->apellido) .
                                ' · CC ' .
                                $clienteSeleccionado->documento
                            : '';
                    @endphp
                    <datalist id="clientes-disponibles">
                        @foreach ($clientes as $cliente)
                            <option
                                value="{{ trim($cliente->nombre . ' ' . $cliente->apellido) }} · CC {{ $cliente->documento }}"
                                data-id="{{ $cliente->id }}"></option>
                            <option
                                value="{{ $cliente->documento }} · {{ trim($cliente->nombre . ' ' . $cliente->apellido) }}"
                                data-id="{{ $cliente->id }}"></option>
                        @endforeach
                    </datalist>
                @endif
                <datalist id="productos-disponibles">
                    @foreach ($productos as $producto)
                        <option value="{{ $producto->nombre }} · Stock: {{ $producto->stock }}"
                            data-id="{{ $producto->id }}" data-price="{{ $producto->precio }}"></option>
                    @endforeach
                </datalist>

                <section class="overflow-hidden rounded-2xl border border-neutral-800 bg-neutral-900/80">
                    <div class="border-b border-neutral-800 px-5 py-4">
                        <h2 class="text-lg font-semibold text-neutral-100">Datos generales de la factura</h2>
                        <p class="mt-1 text-sm text-neutral-400">Los cambios de cliente, fecha y estado se aplican al pulsar
                            Guardar cambios.</p>
                    </div>
                    @if ($puedeEditarCliente || $puedeEditarFactura)
                        <div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-2 xl:grid-cols-3">
                            <div>
                                @if ($puedeEditarCliente)
                                    <label for="buscar-cliente" class="mb-2 block text-sm font-medium text-neutral-300">
                                        Cliente o número de documento
                                    </label>
                                    <input id="buscar-cliente" type="search" list="clientes-disponibles"
                                        value="{{ old('cliente_busqueda', $clienteEtiqueta) }}"
                                        placeholder="Busca por nombre o documento..." autocomplete="off" required
                                        class="w-full rounded-lg border px-3 py-2.5 text-sm">
                                    <input id="id_cliente" type="hidden" name="id_cliente"
                                        value="{{ old('id_cliente', $factura->id_cliente) }}">
                                @else
                                    <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Cliente</p>
                                    <p class="mt-1 font-medium text-neutral-100">
                                        {{ trim(($factura->cliente?->nombre ?? '') . ' ' . ($factura->cliente?->apellido ?? '')) ?: 'Cliente no disponible' }}
                                    </p>
                                    <p class="mt-1 text-xs text-neutral-500">
                                        {{ $factura->cliente?->documento ?? 'Sin documento' }}</p>
                                @endif
                            </div>
                            <div>
                                @if ($puedeEditarFactura)
                                    <label for="fecha" class="mb-2 block text-sm font-medium text-neutral-300">Fecha de
                                        emisión</label>
                                    <input id="fecha" type="date" name="fecha" required
                                        value="{{ old('fecha', $factura->fecha?->format('Y-m-d')) }}"
                                        class="w-full rounded-lg border px-3 py-2.5 text-sm">
                                @else
                                    <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Fecha</p>
                                    <p class="mt-1 font-medium text-neutral-100">
                                        {{ $factura->fecha?->format('d/m/Y') ?? 'Sin fecha' }}</p>
                                @endif
                            </div>
                            @if ($puedeEditarFactura)
                                <div>
                                    <label for="estado"
                                        class="mb-2 block text-sm font-medium text-neutral-300">Estado</label>
                                    <select id="estado" name="estado" required
                                        class="w-full rounded-lg border px-3 py-2.5 text-sm">
                                        @if ($factura->estado === 'pagada')
                                            <option value="pagada" @selected(old('estado', $factura->estado) === 'pagada')>Pagada</option>
                                            <option value="anulada" @selected(old('estado') === 'anulada')>Anulada</option>
                                        @else
                                            <option value="pendiente" @selected(old('estado', $factura->estado) === 'pendiente')>Pendiente</option>
                                            <option value="pagada" @selected(old('estado') === 'pagada')>Pagada</option>
                                            <option value="anulada" @selected(old('estado') === 'anulada')>Anulada</option>
                                        @endif
                                    </select>
                                </div>
                            @endif
                            @if (!$puedeEditarFactura)
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Responsable
                                    </p>
                                    <p class="mt-1 font-medium text-neutral-100">
                                        {{ trim(($factura->usuario?->nombre ?? '') . ' ' . ($factura->usuario?->apellido ?? '')) ?: 'Usuario no disponible' }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Cliente</p>
                                <p class="mt-1 font-medium text-neutral-100">
                                    {{ trim(($factura->cliente?->nombre ?? '') . ' ' . ($factura->cliente?->apellido ?? '')) ?: 'Cliente no disponible' }}
                                </p>
                                <p class="mt-1 text-xs text-neutral-500">
                                    {{ $factura->cliente?->documento ?? 'Sin documento' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Fecha</p>
                                <p class="mt-1 font-medium text-neutral-100">
                                    {{ $factura->fecha?->format('d/m/Y') ?? 'Sin fecha' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Responsable</p>
                                <p class="mt-1 font-medium text-neutral-100">
                                    {{ trim(($factura->usuario?->nombre ?? '') . ' ' . ($factura->usuario?->apellido ?? '')) ?: 'Usuario no disponible' }}
                                </p>
                            </div>
                        </div>
                    @endif
                </section>

                <section class="overflow-hidden rounded-2xl border border-neutral-800 bg-neutral-900/80">
                    <div
                        class="flex flex-col gap-2 border-b border-neutral-800 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-neutral-100">Productos y detalles de factura</h2>
                            <p class="mt-1 text-sm text-neutral-400">Edita producto, cantidad, precio unitario, agrega o
                                retira renglones. Nada se guarda hasta pulsar el botón final.</p>
                        </div>
                        <span
                            class="rounded-full bg-neutral-800 px-3 py-1 text-xs font-medium text-neutral-300">{{ $factura->detalle_factura->count() }}
                            detalle(s)</span>
                    </div>

                    @if ($factura->estado === 'pagada')
                        <p class="border-b border-blue-500/20 bg-blue-500/5 px-5 py-3 text-sm text-blue-200">
                            Las facturas pagadas también se pueden editar. Los ajustes de existencias y sus movimientos se
                            registran al guardar.
                        </p>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-full border-collapse" data-no-column-filters="true">
                            <thead>
                                <tr class="bg-neutral-950/40">
                                    <th
                                        class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-neutral-400">
                                        Producto</th>
                                    <th
                                        class="w-28 px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-neutral-400">
                                        Cantidad</th>
                                    <th
                                        class="w-40 px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-neutral-400">
                                        Precio unitario</th>
                                    <th
                                        class="w-40 px-3 py-3 text-right text-xs font-semibold uppercase tracking-wide text-neutral-400">
                                        Subtotal</th>
                                    @if ($puedeEliminarDetalle || $puedeAgregarDetalle)
                                        <th
                                            class="w-32 px-3 py-3 text-center text-xs font-semibold uppercase tracking-wide text-neutral-400">
                                            Acciones</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody id="detalles-factura">
                                @forelse ($factura->detalle_factura as $detalle)
                                    @php
                                        $productoActual = $detalle->producto;
                                        $productoActivoEnLista = $productos->contains('id', $detalle->id_producto);
                                    @endphp
                                    <tr class="border-t border-neutral-800 align-top" data-fila-detalle
                                        data-subtotal-inicial="{{ $detalle->subtotal }}">
                                        <td class="px-3 py-3">
                                            @if ($puedeEditarDetalle)
                                                <input type="hidden" name="detalles[{{ $loop->index }}][id]"
                                                    value="{{ $detalle->id }}">
                                                <input type="search" list="productos-disponibles" data-product-search
                                                    required
                                                    value="{{ $productoActivoEnLista
                                                        ? $productoActual->nombre . ' · Stock: ' . $productoActual->stock
                                                        : ($productoActual?->nombre ?? 'Producto no disponible') . ' (inactivo)' }}"
                                                    placeholder="Busca un producto..." autocomplete="off"
                                                    class="w-full min-w-0 rounded-lg border px-2 py-2 text-sm">
                                                <input type="hidden" name="detalles[{{ $loop->index }}][id_producto]"
                                                    value="{{ $detalle->id_producto }}" data-product-id>
                                            @else
                                                <span
                                                    class="font-medium text-neutral-100">{{ $productoActual?->nombre ?? 'Producto no disponible' }}</span>
                                            @endif
                                            <p class="mt-1 text-xs text-neutral-500">Detalle #{{ $detalle->id }}</p>
                                        </td>
                                        <td class="px-3 py-3">
                                            @if ($puedeEditarDetalle)
                                                <input type="number" name="detalles[{{ $loop->index }}][cantidad]"
                                                    min="1" required value="{{ $detalle->cantidad }}"
                                                    data-quantity
                                                    class="w-full min-w-0 rounded-lg border px-2 py-2 text-sm">
                                            @else
                                                <span>{{ $detalle->cantidad }}</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3">
                                            @if ($puedeEditarDetalle)
                                                <input type="number"
                                                    name="detalles[{{ $loop->index }}][precio_unitario]" min="0"
                                                    step="0.01" required value="{{ $detalle->precio_unitario }}"
                                                    data-price-input
                                                    class="w-full min-w-0 rounded-lg border px-2 py-2 text-sm">
                                            @else
                                                <span>$
                                                    {{ number_format((float) $detalle->precio_unitario, 2, ',', '.') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-right font-semibold text-neutral-100">
                                            $ <span
                                                data-line-subtotal>{{ number_format((float) $detalle->subtotal, 2, ',', '.') }}</span>
                                        </td>
                                        @if ($puedeEliminarDetalle || $puedeAgregarDetalle)
                                            <td class="px-3 py-3 text-center">
                                                @if ($puedeEliminarDetalle)
                                                    <button type="button" data-remove-existing="{{ $detalle->id }}"
                                                        class="rounded-lg border border-red-500/40 px-3 py-2 text-xs font-semibold text-red-300 transition hover:bg-red-500/10">Retirar</button>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr id="sin-detalles">
                                        <td colspan="{{ $puedeEliminarDetalle || $puedeAgregarDetalle ? 5 : 4 }}"
                                            class="px-4 py-10 text-center text-sm text-neutral-400">
                                            Esta factura todavía no tiene productos asociados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <template id="plantilla-detalle">
                                <tr class="border-t border-neutral-800 align-top" data-fila-detalle>
                                    <td class="px-3 py-3">
                                        <input type="search" list="productos-disponibles" data-product-search required
                                            placeholder="Busca un producto..." autocomplete="off"
                                            class="w-full min-w-0 rounded-lg border px-2 py-2 text-sm">
                                        <input type="hidden" name="detalles[__INDEX__][id_producto]" data-product-id>
                                        <p class="mt-1 text-xs text-neutral-500">Nuevo detalle</p>
                                    </td>
                                    <td class="px-3 py-3">
                                        <input type="number" name="detalles[__INDEX__][cantidad]" min="1"
                                            value="1" required data-quantity
                                            class="w-full min-w-0 rounded-lg border px-2 py-2 text-sm">
                                    </td>
                                    <td class="px-3 py-3">
                                        <input type="number" name="detalles[__INDEX__][precio_unitario]" min="0"
                                            step="0.01" required data-price-input
                                            class="w-full min-w-0 rounded-lg border px-2 py-2 text-sm">
                                    </td>
                                    <td class="px-3 py-3 text-right font-semibold text-neutral-100">$ <span
                                            data-line-subtotal>0,00</span></td>
                                    @if ($puedeEliminarDetalle || $puedeAgregarDetalle)
                                        <td class="px-3 py-3 text-center">
                                            <button type="button" data-remove-new
                                                class="rounded-lg border border-red-500/40 px-3 py-2 text-xs font-semibold text-red-300 transition hover:bg-red-500/10">Quitar</button>
                                        </td>
                                    @endif
                                </tr>
                            </template>
                        </table>
                    </div>

                    <div id="eliminaciones-detalles"></div>

                    <div
                        class="flex flex-col gap-4 border-t border-neutral-800 bg-neutral-950/40 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        @if ($puedeAgregarDetalle)
                            <button type="button" id="agregar-detalle"
                                class="rounded-lg border border-blue-500/40 bg-blue-500/10 px-4 py-2.5 text-sm font-semibold text-blue-200 transition hover:bg-blue-500/20">
                                Agregar producto
                            </button>
                        @else
                            <span class="text-xs text-neutral-500">No tienes permiso para agregar productos.</span>
                        @endif
                        <div class="grid w-full max-w-md grid-cols-3 gap-3 text-sm sm:w-auto">
                            <div>
                                <p class="text-xs text-neutral-400">Subtotal</p>
                                <p class="mt-1 font-semibold text-neutral-100">$ <span
                                        id="total-subtotal">{{ number_format((float) $factura->subtotal, 2, ',', '.') }}</span>
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">IVA (19 %)</p>
                                <p class="mt-1 font-semibold text-neutral-100">$ <span
                                        id="total-iva">{{ number_format((float) $factura->iva, 2, ',', '.') }}</span></p>
                            </div>
                            <div>
                                <p class="text-xs text-neutral-400">Total</p>
                                <p class="mt-1 font-bold text-blue-300">$ <span
                                        id="total-general">{{ number_format((float) $factura->total, 2, ',', '.') }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <div
                    class="flex flex-col-reverse gap-3 border-t border-neutral-800 pt-5 sm:flex-row sm:items-center sm:justify-end">
                    <a href="{{ route('facturas.show', $factura->id) }}"
                        class="rounded-lg border border-neutral-700 px-5 py-2.5 text-center text-sm font-semibold text-neutral-200 transition hover:bg-neutral-800">Cancelar</a>
                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">Guardar
                        cambios</button>
                </div>
            </form>
        @else
            <section class="overflow-hidden rounded-2xl border border-neutral-800 bg-neutral-900/80">
                <div class="border-b border-neutral-800 px-5 py-4">
                    <h2 class="text-lg font-semibold text-neutral-100">Datos generales de la factura</h2>
                </div>
                <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Cliente</p>
                        <p class="mt-1 font-medium text-neutral-100">
                            {{ trim(($factura->cliente?->nombre ?? '') . ' ' . ($factura->cliente?->apellido ?? '')) ?: 'Cliente no disponible' }}
                        </p>
                        <p class="mt-1 text-xs text-neutral-500">{{ $factura->cliente?->documento ?? 'Sin documento' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Fecha</p>
                        <p class="mt-1 font-medium text-neutral-100">
                            {{ $factura->fecha?->format('d/m/Y') ?? 'Sin fecha' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-neutral-500">Responsable</p>
                        <p class="mt-1 font-medium text-neutral-100">
                            {{ trim(($factura->usuario?->nombre ?? '') . ' ' . ($factura->usuario?->apellido ?? '')) ?: 'Usuario no disponible' }}
                        </p>
                    </div>
                </div>
            </section>
            <section class="overflow-hidden rounded-2xl border border-neutral-800 bg-neutral-900/80">
                <div class="border-b border-neutral-800 px-5 py-4">
                    <h2 class="text-lg font-semibold text-neutral-100">Productos y detalles de factura</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="datatable w-full min-w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-neutral-400">Producto
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-neutral-400">Cantidad
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-neutral-400">Precio
                                    unitario</th>
                                <th class="px-3 py-3 text-right text-xs font-semibold uppercase text-neutral-400">Subtotal
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($factura->detalle_factura as $detalle)
                                <tr class="border-t border-neutral-800">
                                    <td class="px-3 py-3 text-neutral-100">
                                        {{ $detalle->producto?->nombre ?? 'Producto no disponible' }}</td>
                                    <td class="px-3 py-3 text-neutral-200">{{ $detalle->cantidad }}</td>
                                    <td class="px-3 py-3 text-neutral-200">$
                                        {{ number_format((float) $detalle->precio_unitario, 2, ',', '.') }}</td>
                                    <td class="px-3 py-3 text-right font-semibold text-neutral-100">$
                                        {{ number_format((float) $detalle->subtotal, 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-10 text-center text-sm text-neutral-400">Esta
                                        factura no tiene productos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-neutral-800 bg-neutral-950/40 px-5 py-4">
                    <div class="ml-auto grid max-w-md grid-cols-3 gap-3 text-sm">
                        <div>
                            <p class="text-xs text-neutral-400">Subtotal</p>
                            <p class="mt-1 font-semibold text-neutral-100">$
                                {{ number_format((float) $factura->subtotal, 2, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-400">IVA (19 %)</p>
                            <p class="mt-1 font-semibold text-neutral-100">$
                                {{ number_format((float) $factura->iva, 2, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-neutral-400">Total</p>
                            <p class="mt-1 font-bold text-blue-300">$
                                {{ number_format((float) $factura->total, 2, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                @if ($factura->estado === 'anulada')
                    <p class="border-t border-neutral-800 px-5 py-3 text-sm text-neutral-400">Esta factura está anulada y
                        se conserva como historial. Sus datos y detalles son de solo lectura.</p>
                @else
                    <p class="border-t border-neutral-800 px-5 py-3 text-sm text-neutral-400">Tu cuenta no tiene permisos
                        para modificar esta factura ni sus detalles.</p>
                @endif
            </section>
        @endif
    </div>
@endsection

@push('scripts')
    @if ($puedeGuardarCambios)
        <script>
            (() => {
                const form = document.getElementById('form-editar-factura');
                const tbody = document.getElementById('detalles-factura');
                const template = document.getElementById('plantilla-detalle');
                const addButton = document.getElementById('agregar-detalle');
                const deleteContainer = document.getElementById('eliminaciones-detalles');
                const productosDisponibles = document.getElementById('productos-disponibles');
                const clienteBusqueda = document.getElementById('buscar-cliente');
                const clienteId = document.getElementById('id_cliente');
                const clientesDisponibles = document.getElementById('clientes-disponibles');
                let nextIndex = {{ $factura->detalle_factura->count() }};

                const sincronizarCliente = () => {
                    if (!clienteBusqueda || !clienteId || !clientesDisponibles) return;
                    const opcion = Array.from(clientesDisponibles.options)
                        .find((item) => item.value === clienteBusqueda.value);
                    clienteId.value = opcion?.dataset.id || '';
                    clienteBusqueda.setCustomValidity('');
                };

                const formatoMoneda = (valor) => new Intl.NumberFormat('es-CO', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(valor);

                const recalcularTotales = () => {
                    let subtotalGeneral = 0;

                    tbody.querySelectorAll('[data-fila-detalle]').forEach((row) => {
                        const cantidadInput = row.querySelector('[data-quantity]');
                        const precioInput = row.querySelector('[data-price-input]');
                        const subtotal = cantidadInput && precioInput ?
                            Math.max(0, (Number(cantidadInput.value) || 0) * (Number(precioInput.value) || 0)) :
                            Number(row.dataset.subtotalInicial || 0);
                        const subtotalElemento = row.querySelector('[data-line-subtotal]');

                        if (subtotalElemento) subtotalElemento.textContent = formatoMoneda(subtotal);
                        subtotalGeneral += subtotal;
                    });

                    const iva = subtotalGeneral * 0.19;
                    document.getElementById('total-subtotal').textContent = formatoMoneda(subtotalGeneral);
                    document.getElementById('total-iva').textContent = formatoMoneda(iva);
                    document.getElementById('total-general').textContent = formatoMoneda(subtotalGeneral + iva);
                };

                const prepararFila = (row) => {
                    const busquedaProducto = row.querySelector('[data-product-search]');
                    const productoId = row.querySelector('[data-product-id]');
                    const priceInput = row.querySelector('[data-price-input]');

                    if (busquedaProducto && productoId && priceInput) {
                        const sincronizarProducto = () => {
                            const opcion = Array.from(productosDisponibles.options)
                                .find((item) => item.value === busquedaProducto.value);
                            productoId.value = opcion?.dataset.id || '';
                            if (opcion?.dataset.price) priceInput.value = opcion.dataset.price;
                            busquedaProducto.setCustomValidity('');
                            recalcularTotales();
                        };

                        busquedaProducto.addEventListener('input', sincronizarProducto);
                        busquedaProducto.addEventListener('change', sincronizarProducto);
                    }

                    row.querySelectorAll('[data-quantity], [data-price-input]').forEach((input) => {
                        input.addEventListener('input', recalcularTotales);
                    });

                    row.querySelector('[data-remove-existing]')?.addEventListener('click', (event) => {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'eliminar_detalles[]';
                        hidden.value = event.currentTarget.dataset.removeExisting;
                        deleteContainer.appendChild(hidden);
                        row.remove();
                        recalcularTotales();
                    });

                    row.querySelector('[data-remove-new]')?.addEventListener('click', () => {
                        row.remove();
                        recalcularTotales();
                    });
                };

                tbody.querySelectorAll('[data-fila-detalle]').forEach(prepararFila);

                if (addButton && template) {
                    addButton.addEventListener('click', () => {
                        const html = template.innerHTML.replaceAll('__INDEX__', String(nextIndex++));
                        const wrapper = document.createElement('tbody');
                        wrapper.innerHTML = html.trim();
                        const row = wrapper.querySelector('tr');
                        if (!row) return;
                        tbody.querySelector('#sin-detalles')?.remove();
                        tbody.appendChild(row);
                        prepararFila(row);
                        recalcularTotales();
                    });
                }

                form.addEventListener('submit', (event) => {
                    if (clienteBusqueda && clienteId) {
                        sincronizarCliente();
                        if (!clienteId.value) {
                            event.preventDefault();
                            clienteBusqueda.setCustomValidity('Selecciona un cliente de la lista para continuar.');
                            clienteBusqueda.reportValidity();
                            return;
                        }
                    }

                    let productoInvalido = null;
                    tbody.querySelectorAll('[data-fila-detalle]').forEach((row) => {
                        const busqueda = row.querySelector('[data-product-search]');
                        const id = row.querySelector('[data-product-id]');
                        if (busqueda && busqueda.value.trim() && id && !id.value) {
                            busqueda.setCustomValidity('Selecciona un producto de la lista.');
                            productoInvalido = productoInvalido || busqueda;
                        }
                    });

                    if (productoInvalido) {
                        event.preventDefault();
                        productoInvalido.reportValidity();
                        return;
                    }

                    form.querySelectorAll('button[type="submit"]').forEach((button) => {
                        button.disabled = true;
                        button.textContent = 'Guardando...';
                    });
                });

                recalcularTotales();
            })();
        </script>
    @endif
@endpush
