@extends('layouts.app')

@section('title', 'Crear Producto')

@section('content')
    <div class="container mx-auto mt-10">

        <div class="max-w-xl mx-auto bg-white shadow-lg rounded-lg p-8">

            <h2 class="text-3xl font-bold text-center mb-6">

                Nuevo Producto

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

            <form action="{{ route('productos.store')}}" method="post">
            @csrf

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Nombre</label>
                    <input type="text" name="nombre" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Descripción</label>
                    <input type="text" name="descripcion" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Precio</label>
                    <input type="number" name="precio" step="0.01" class="w-full border rounded px-3 py-2">
                </div> 
                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Stock</label>
                    <input type="number" name="stock" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Imagen</label>
                    <input type="text" name="imagen" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Estado</label>
                    <select name="estado" class="w-full border rounded px-3 py-2">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Categoría</label>
                    <select name="categoria_id" class="w-full border rounded px-3 py-2">
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre_categoria }}</option>
                        @endforeach
                </select>
                </div>

                <div class="mb-5">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white rounded px-5 py-2">Guardar</button>
                </div>   
            
            </form>

        </div>

    </div>
@endsection