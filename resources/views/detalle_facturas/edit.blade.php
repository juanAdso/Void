@extends('layouts.app')

@section('title', 'Editar Detalle de Factura')

@section('content')
    <div class="container mx-auto mt-10">

        <div class="max-w-xl mx-auto bg-white shadow-lg rounded-lg p-8">

            <h2 class="text-3xl font-bold text-center mb-6">
                Editar Detalle de Factura
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

            <form action="{{ route('detalle_facturas.update', $detalle_factura->id) }}" method="post">
                @csrf
                @method('PUT')

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Factura</label>
                    <select name="id_factura" class="w-full border rounded px-3 py-2">
                        @foreach($facturas as $factura)
                            <option value="{{ $factura->id }}" {{ $detalle_factura->id_factura == $factura->id ? 'selected' : '' }}>Factura #{{ $factura->id }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Producto</label>
                    <select name="id_producto" class="w-full border rounded px-3 py-2">
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}" {{ $detalle_factura->id_producto == $producto->id ? 'selected' : '' }}>{{ $producto->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Cantidad</label>
                    <input type="number" name="cantidad" value="{{ $detalle_factura->cantidad }}" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Precio Unitario</label>
                    <input type="number" name="precio_unitario" step="0.01" value="{{ $detalle_factura->precio_unitario }}" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Subtotal</label>
                    <input type="number" name="subtotal" step="0.01" value="{{ $detalle_factura->subtotal }}" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-5">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white rounded px-5 py-2">Guardar</button>
                </div>

            </form>

        </div>

    </div>
@endsection
