<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Registrar tienda</title></head>
<body>
    <h1>Registrar tienda</h1>
    @if ($errors->any()) <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul> @endif
    <form action="{{ route('tiendas.store') }}" method="POST">
        @csrf
        @include('tiendas._form', ['tienda' => null])
        <button type="submit">Guardar tienda</button>
    </form>
    <p><a href="{{ route('tiendas.index') }}">Volver a tiendas</a></p>
</body>
</html>
