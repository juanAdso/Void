@extends('layouts.app')

@section('title', 'Editar Categoría')

@section('content')
    <div class="container mx-auto mt-10">

        <div class="max-w-xl mx-auto bg-white shadow-lg rounded-lg p-8">

            <h2 class="text-3xl font-bold text-center mb-6">
                Editar Categoría
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

            <form action="{{ route('categoria.update', $categoria->id_categoria) }}" method="post">
                @csrf
                @method('PUT')

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Nombre Categoria</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $categoria->nombre) }}" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Descripcion</label>
                    <input type="text" name="descripcion" value="{{ old('descripcion', $categoria->descripcion) }}" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Estado</label>
                    <select name="estado" class="w-full border rounded px-3 py-2">
                        <option value="1" @selected(old('estado', $categoria->estado) == 1)>Activo</option>
                        <option value="0" @selected(old('estado', $categoria->estado) == 0)>Inactivo</option>
                    </select>
                </div>

                <div class="mb-5">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white rounded px-5 py-2">Guardar</button>
                </div>

            </form>

        </div>

    </div>
@endsection
