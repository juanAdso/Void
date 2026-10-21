<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Tiendas</title></head>
<body>
    <h1>Tiendas</h1>
    @if (session('success')) <p>{{ session('success') }}</p> @endif
    @if ($errors->any()) <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul> @endif
    <p><a href="{{ route('tiendas.create') }}">Registrar tienda</a></p>
    @if ($tiendas->isEmpty())
        <p>No hay tiendas registradas.</p>
    @else
        <table border="1" cellpadding="8" cellspacing="0">
            <thead><tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Teléfono</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
            @foreach ($tiendas as $tienda)
                <tr>
                    <td>{{ $tienda->id }}</td><td>{{ $tienda->nombre }}</td>
                    <td>{{ $tienda->correo ?? 'No registrado' }}</td><td>{{ $tienda->telefono ?? 'No registrado' }}</td>
                    <td>{{ $tienda->estado ? 'Activa' : 'Inactiva' }}</td>
                    <td>
                        <a href="{{ route('tiendas.show', $tienda->id) }}">Ver</a>
                        <a href="{{ route('tiendas.edit', $tienda->id) }}">Editar</a>
                        <form action="{{ route('tiendas.destroy', $tienda->id) }}" method="POST" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('¿Deseas eliminar esta tienda?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
