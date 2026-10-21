<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Detalle de tienda</title></head>
<body>
    <h1>{{ $tienda->nombre }}</h1>
    <p><strong>Descripción:</strong> {{ $tienda->descripcion ?? 'Sin descripción' }}</p>
    <p><strong>Logo:</strong> {{ $tienda->logo ?? 'No registrado' }}</p>
    <p><strong>Dirección:</strong> {{ $tienda->direccion ?? 'No registrada' }}</p>
    <p><strong>Teléfono:</strong> {{ $tienda->telefono ?? 'No registrado' }}</p>
    <p><strong>Correo:</strong> {{ $tienda->correo ?? 'No registrado' }}</p>
    <p><strong>Estado:</strong> {{ $tienda->estado ? 'Activa' : 'Inactiva' }}</p>
    <p><a href="{{ route('tiendas.edit', $tienda->id) }}">Editar tienda</a></p>
    <p><a href="{{ route('tiendas.index') }}">Volver a tiendas</a></p>
</body>
</html>
