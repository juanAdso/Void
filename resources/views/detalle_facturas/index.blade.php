@extends('layouts.app')


@section('titulo')
    Detalle Facturas
@endsection


@section('contenido')

<div class="container mx-auto mt-10">

        <div class="bg-white shadow-lg rounded-lg p-6">

            <div class="flex justify-between items-center mb-6">

                <h2 class="text-3xl font-bold text-gray-700">
                    Detalle de Facturas
                </h2>

                <a href="{{ route('detalle_facturas.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">

                    Nuevo Detalle de Factura
                </a>

            </div>


            @if (session('store'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('store') }}
                </div>
            @endif

            @if (session('edit'))
                <div class="bg-yellow-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('edit') }}
                </div>
            @endif

            @if (session('delete'))
                <div class="bg-red-100 border border-red-400 text-red-500 px-4 py-3 rounded mb-4">
                    {{ session('delete') }}
                </div>
            @endif



            <table class="min-w-full border border-gray-200">

                <thead class="bg-gray-200">

                    <tr>

                        <th class="border px-4 py-2">
                            ID Detalle Factura
                        </th>

                        <th class="border px-4 py-2">
                            ID Factura
                        </th>

                        <th class="border px-4 py-2">
                            ID Producto
                        </th>

                        <th class="border px-4 py-2">
                            Cantidad
                        </th>

                        <th class="border px-4 py-2">
                            Precio Unitario
                        </th>

                        <th class="border px-4 py-2">
                            Total
                        </th>

                        <th class="border px-4 py-2">
                            Acciones
                        </th>

                    </tr>

                </thead>

                <tbody>

                @foreach ($detalle_facturas  as $detalle_facturas)

                    <tr class="text-center hover:bg-gray-50">
                        <td class="border px-4 py-2">{{ $detalle_facturas->id}}</td>
                        <td class="border px-4 py-2">{{ $detalle_facturas->facturas->id}}</td>
                        <td class="border px-4 py-2">{{ $detalle_facturas->productos->id}}</td>
                        <td class="border px-4 py-2">{{ $detalle_facturas->cantidad}}</td>
                        <td class="border px-4 py-2">{{ $detalle_facturas->precio_unitario}}</td>
                        <td class="border px-4 py-2">{{ $detalle_facturas->total}}</td>

                        

                        <td class="border px-4 py-2">


                        <br>
                            <a href="{{ route('detalle_facturas.edit', $detalle_facturas->id)}}" class="max-w-xl mx-auto bg-purple-400 shadow-lg rounded-lg p-3 my-4">Editar</a>

                            <form action="{{ route('detalle_facturas.destroy', $detalle_facturas->id)}}" method="post">
                                @csrf
                                @method('DELETE')

                                <br>
                                <button class="max-w-xl mx-auto bg-red-400 shadow-lg rounded-lg p-3">eliminar</button>
                                
                            </form>

                        </td>
                    </tr>

                @endforeach

                


                </tbody>

            </table>

        </div>

    </div>


@endsection