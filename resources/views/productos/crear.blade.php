@extends('layouts.app')

@section('title', 'Crear Producto')

@section('content')
    <div class="container mx-auto mt-10">
        <div class="max-w-xl mx-auto bg-white shadow-lg rounded-lg p-8">
            <h2 class="text-3xl font-bold text-center mb-6">Nuevo Producto</h2>

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-5">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('productos.store') }}" method="post">
                @csrf
                <div class="mb-5">
                    <label for="nombre" class="block mb-2 font-semibold">Nombre</label>
                    <input id="nombre" type="text" name="nombre" value="{{ old('nombre') }}" maxlength="150" required class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="descripcion" class="block mb-2 font-semibold">Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="w-full border rounded px-3 py-2">{{ old('descripcion') }}</textarea>
                </div>
                <div class="mb-5">
                    <label for="precio" class="block mb-2 font-semibold">Precio</label>
                    <input id="precio" type="number" name="precio" value="{{ old('precio') }}" min="0" step="0.01" required class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="stock" class="block mb-2 font-semibold">Stock</label>
                    <input id="stock" type="number" name="stock" value="{{ old('stock', 0) }}" min="0" required class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="imagen" class="block mb-2 font-semibold">Ruta de imagen</label>
                    <input id="imagen" type="text" name="imagen" value="{{ old('imagen') }}" maxlength="255" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="estado" class="block mb-2 font-semibold">Estado</label>
                    <select id="estado" name="estado" required class="w-full border rounded px-3 py-2">
                        <option value="1" @selected(old('estado', '1') == '1')>Activo</option>
                        <option value="0" @selected(old('estado') == '0')>Inactivo</option>
                    </select>
                </div>
                <div class="mb-5">
                    <label for="id_categoria" class="block mb-2 font-semibold">Categoría</label>
                    <select id="id_categoria" name="id_categoria" required class="w-full border rounded px-3 py-2">
                        <option value="">Selecciona una categoría</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id_categoria }}" @selected(old('id_categoria') == $categoria->id_categoria)>{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white rounded px-5 py-2">Guardar</button>
                    <a href="{{ route('productos.index') }}" class="border rounded px-5 py-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
