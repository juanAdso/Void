@extends('layouts.app')

@section('title', 'Bienvenido')

@section('content')
    <x-card>
        <div class="container mx-auto p-6 text-center">
            <h1 class="text-3xl font-bold mb-6">Bienvenido a la aplicación Void</h1>
            <div class="flex justify-center gap-4">
                <a href="{{ route('categoria.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Crud Categorías</a>
                <a href="{{ route('cliente.index') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Crud Clientes</a>
                <a href="{{ route('productos.index') }}" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Crud Productos</a>
                <a href="{{ route('facturas.index') }}" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">Crud Facturas</a>
            </div>
        </div>
    </x-card>
@endsection
