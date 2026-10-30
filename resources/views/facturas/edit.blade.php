@extends('layouts.app')

@section('title', 'Editar Factura')

@section('content')
    <div class="container mx-auto mt-10">
        <div class="mx-auto max-w-xl rounded-lg bg-white p-8 shadow-lg">
            <h2 class="mb-6 text-center text-3xl font-bold">Editar Factura #{{ $facturas->id }}</h2>

            @if ($errors->any())
                <div class="mb-5 rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('facturas.update', $facturas->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-5">
                    <label for="id_cliente" class="mb-2 block font-semibold">Cliente</label>
                    <select id="id_cliente" name="id_cliente" required class="w-full rounded border px-3 py-2">
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}" @selected(old('id_cliente', $facturas->id_cliente) == $cliente->id)>{{ $cliente->nombre }} {{ $cliente->apellido }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-5">
                    <label for="fecha" class="mb-2 block font-semibold">Fecha</label>
                    <input id="fecha" type="date" name="fecha" value="{{ old('fecha', $facturas->fecha?->format('Y-m-d')) }}" required class="w-full rounded border px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="estado" class="mb-2 block font-semibold">Estado</label>
                    <select id="estado" name="estado" required class="w-full rounded border px-3 py-2">
                        <option value="pendiente" @selected(old('estado', $facturas->estado) === 'pendiente')>Pendiente</option>
                        <option value="pagada" @selected(old('estado', $facturas->estado) === 'pagada')>Pagada</option>
                        <option value="anulada" @selected(old('estado', $facturas->estado) === 'anulada')>Anulada</option>
                    </select>
                </div>
                <div class="mb-5 rounded bg-slate-50 p-4 text-sm text-slate-600">
                    <p>Subtotal: {{ number_format((float) $facturas->subtotal, 2, ',', '.') }}</p>
                    <p>IVA: {{ number_format((float) $facturas->iva, 2, ',', '.') }}</p>
                    <p class="font-bold">Total: {{ number_format((float) $facturas->total, 2, ',', '.') }}</p>
                    <p class="mt-2">Los valores se actualizan desde los detalles de la factura.</p>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="rounded bg-green-600 px-5 py-2 text-white hover:bg-green-700">Guardar cambios</button>
                    <a href="{{ route('facturas.index') }}" class="rounded border px-5 py-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
