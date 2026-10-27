@extends('layouts.app')

@section('title', 'Editar Factura')

@section('content')
    <div class="container mx-auto mt-10">

        <div class="max-w-xl mx-auto bg-white shadow-lg rounded-lg p-8">

            <h2 class="text-3xl font-bold text-center mb-6">
                Editar Factura
            </h2>
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-5">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('facturas.update', $facturas->id) }}" method="post">
                @csrf
                @method('PUT')

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Cliente</label>
                    <select name="id_cliente" required class="w-full border rounded px-3 py-2">
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ $facturas->id_cliente == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Fecha</label>
                    <input type="date" name="fecha" value="{{ $facturas->fecha ? $facturas->fecha->format('Y-m-d') : '' }}" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Subtotal</label>
                    <input type="number" name="subtotal" step="0.01" value="{{ $facturas->subtotal }}" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Iva</label>
                    <input type="number" name="iva" step="0.01" value="{{ $facturas->iva }}" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Total</label>
                    <input type="number" name="total" step="0.01" value="{{ $facturas->total }}" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Estado</label>
                    <select name="estado" required class="w-full border rounded px-3 py-2">
                        <option value="pendiente" @selected(old('estado', $facturas->estado) === 'pendiente')>Pendiente</option>
                        <option value="pagada" @selected(old('estado', $facturas->estado) === 'pagada')>Pagada</option>
                        <option value="anulada" @selected(old('estado', $facturas->estado) === 'anulada')>Anulada</option>
                    </select>
                </div>

                <div class="mb-5">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white rounded px-5 py-2">Guardar</button>
                </div>

            </form>

        </div>

    </div>
@endsection
