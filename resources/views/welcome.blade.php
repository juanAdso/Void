@extends('layouts.app')
@section('Void')
Welcome
@endsection
<a href="{{ route('categoria.index') }}">Crud Categoria</a>
<a href="{{ route('cliente.index') }}">Crud Cliente</a>
<a href="{{ route('productos.index') }}">Crud Productos</a>
<br>
@section('contenido')