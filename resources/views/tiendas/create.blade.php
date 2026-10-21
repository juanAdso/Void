@extends('layouts.app')

@section('title', 'Registrar tienda')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <a href="{{ route('tiendas.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-800">&larr; Volver a tiendas</a>
            <h1 class="mt-3 text-3xl font-bold text-slate-800">Registrar tienda</h1>
            <p class="mt-1 text-sm text-slate-500">Completa los datos de la tienda que quieres agregar a VOID.</p>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <p class="font-semibold">Revisa los siguientes campos:</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <form action="{{ route('tiendas.store') }}" method="POST" class="space-y-6">
                @csrf
                @include('tiendas._form', ['tienda' => null])
                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('tiendas.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancelar</a>
                    <button type="submit" class="rounded-lg bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Guardar tienda</button>
                </div>
            </form>
        </div>
    </div>
@endsection
