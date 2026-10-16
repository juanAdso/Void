@extends('layouts.app')

@section('title', 'Editar Factura')

@section('content')
    @php
        $factura = $factura ?? $facturas;
    @endphp
    <div class="container mx-auto mt-10">
        <div class="max-w-xl mx-auto bg-white shadow-lg rounded-lg p-8">
            <h2 class="text-3xl font-bold text-center mb-6">Editar Factura</h2>

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-5">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('facturas.update', $factura->id) }}" method="post">
                @csrf
                @method('PUT')

                <div class="mb-5">
                    <label class="block mb-2 font-semibold">Cliente</label>
                    <select name="id_cliente" class="w-full border rounded px-3 py-2">
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ $factura->id_cliente == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }} {{ $cliente->apellido }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-5">
                    <label class="block mb-2 font-semibold">Fecha</label>
                    <input type="date" name="fecha" value="{{ $factura->fecha ? $factura->fecha->format('Y-m-d') : '' }}" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-5">
                    <label class="block mb-2 font-semibold">Subtotal</label>
                    <input type="number" step="0.01" name="subtotal" value="{{ $factura->subtotal }}" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-5">
                    <label class="block mb-2 font-semibold">IVA</label>
                    <input type="number" step="0.01" name="iva" value="{{ $factura->iva }}" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-5">
                    <label class="block mb-2 font-semibold">Total</label>
                    <input type="number" step="0.01" name="total" value="{{ $factura->total }}" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-5">
                    <label class="block mb-2 font-semibold">Estado</label>
                    <select name="estado" class="w-full border rounded px-3 py-2">
                        <option value="Pendiente" {{ $factura->estado === 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="Pagada" {{ $factura->estado === 'Pagada' ? 'selected' : '' }}>Pagada</option>
                        <option value="Anulada" {{ $factura->estado === 'Anulada' ? 'selected' : '' }}>Anulada</option>
                    </select>
                </div>

                <div class="mb-5">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white rounded px-5 py-2">Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
