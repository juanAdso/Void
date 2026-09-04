@extends('layouts.app')

@section('title')
    Editar Cliente

@section('content')
    <div class="container mx-auto mt-10">

        <div class="max-w-xl mx-auto bg-white shadow-lg rounded-lg p-8">

            <h2 class="text-3xl font-bold text-center mb-6">
                Editar Cliente
            </h2>

            <form action="{{ route('cliente.update',$cliente->id)}}" method="post">
            @csrf
            @method('PUT')

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Nombre Cliente</label>
                    <input type="text" name="nombre" value="{{$cliente->nombre}}" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Apellido</label>
                    <input type="text" name="apellido" value="{{$cliente->apellido}}" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Documento</label>
                    <input type="text" name="documento" value="{{$cliente->documento}}" class="w-full border rounded px-3 py-2">
                
                </div>
                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Telefono</label>
                    <input type="text" name="telefono" value="{{$cliente->telefono}}" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Correo</label>
                    <input type="email" name="correo" value="{{$cliente->correo}}" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="" class="block mb-2 font-semibold">Dirección</label>
                    <input type="text" name="direccion" value="{{$cliente->direccion}}" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white rounded px-5 py-2">Guardar</button>
                </div>
            
            </form>

        </div>

    </div>
@endsection


