@extends('layouts.app')
@section('Void')
Welcome
@endsection
<a href="{{ route('categoria.index') }}">Crud Categoria</a>
<a href="{{ route('cliente.index') }}">Crud Cliente</a>
<br>
@section('contenido')