@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
    <div class="container mx-auto mt-10">

        <div class="max-w-xl mx-auto bg-white shadow-lg rounded-lg p-8">

            <h2 class="text-3xl font-bold text-center mb-6">
                Editar Cliente
            </h2>

            @if ($errors->any())
                <div class="mb-5 rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('cliente.update', $cliente->id) }}" method="post">
                @csrf
                @method('PUT')

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Nombre Cliente</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $cliente->nombre) }}" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Apellido</label>
                    <input type="text" name="apellido" value="{{ old('apellido', $cliente->apellido) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Documento</label>
                    <input type="text" name="documento" value="{{ old('documento', $cliente->documento) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Telefono</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $cliente->telefono) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Correo</label>
                    <input type="email" name="correo" value="{{ old('correo', $cliente->correo) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion', $cliente->direccion) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white rounded px-5 py-2">Guardar</button>
                </div>

            </form>

        </div>

    </div>
@endsection
