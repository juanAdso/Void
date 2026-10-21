<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Editar tienda</title></head>
<body>
    <h1>Editar tienda: {{ $tienda->nombre }}</h1>
    @if ($errors->any()) <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul> @endif
    <form action="{{ route('tiendas.update', $tienda->id) }}" method="POST">
        @csrf @method('PUT')
        @include('tiendas._form', ['tienda' => $tienda])
        <button type="submit">Actualizar tienda</button>
    </form>
    <p><a href="{{ route('tiendas.index') }}">Volver a tiendas</a></p>
</body>
</html>
