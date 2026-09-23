@extends('layouts.app')

@section('title')
    Editar Producto

@section('content')
    <div class="container mx-auto mt-10">

        <div class="max-w-xl mx-auto bg-white shadow-lg rounded-lg p-8">

            <h2 class="text-3xl font-bold text-center mb-6">
                Editar Producto
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

            <form action="{{ route('productos.update',$productos->id)}}" method="post">
            @csrf
            @method('PUT')

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Nombre</label>
                    <input type="text" name="nombre" value="{{$productos->nombre}}" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Descripcion</label>
                    <input type="text" name="descripcion" value="{{$productos->descripcion}}" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Precio</label>
                    <input type="text" name="precio" value="{{$productos->precio}}" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Stock</label>
                    <input type="text" name="stock" value="{{$productos->stock}}" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Imagen</label>
                    <input type="text" name="imagen" value="{{$productos->imagen}}" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Estado</label>
                    <select name="estado" class="w-full border rounded px-3 py-2">
                        <option value="1" {{ $productos->estado == 1 ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ $productos->estado == 0 ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Categoría</label>
                    <select name="id_categoria" class="w-full border rounded px-3 py-2">
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