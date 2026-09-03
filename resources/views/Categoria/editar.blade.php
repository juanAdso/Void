@extends('layouts.app')

@section('title')
    Editar Categoria

@section('content')
    <div class="container mx-auto mt-10">

        <div class="max-w-xl mx-auto bg-white shadow-lg rounded-lg p-8">

            <h2 class="text-3xl font-bold text-center mb-6">
                Editar Categoria
            </h2>

            <form action="{{ route('categoria.update',$categoria->id)}}" method="post">
            @csrf
            @method('PUT')

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Nombre Categoria</label>
                    <input type="text" name="nombre_categoria" value="{{$categoria->nombre_categoria}}" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Descripcion</label>
                    <input type="text" name="descripcion" value="{{$categoria->descripcion}}" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Estado</label>
                    <select name="estado" class="w-full border rounded px-3 py-2">
                        <option value="1" {{ $categoria->estado == 1 ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ $categoria->estado == 0 ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>

                <div class="mb-5">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white rounded px-5 py-2">Guardar</button>
                </div>
            
            </form>

        </div>

    </div>
@endsection


