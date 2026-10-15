@extends('layouts.app')
@section('Void')
Welcome
@endsection
<a href="{{ route('categoria.index') }}">Crud Categoria</a>
<a href="{{ route('cliente.index') }}">Crud Cliente</a>
<a href="{{ route('productos.index') }}">Crud Productos</a>
<a href="{{ route('roles.index') }}">Crud Facturas</a>
<a href="{{ route('reports.index') }}">Crud Detalle de Facturas</a>
<br>
@section('contenido')