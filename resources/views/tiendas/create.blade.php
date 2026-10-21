<!DOCTYPE html>
<html>
<head>
    <title>Crear Tienda</title>
</head>
<body>

<h1>Crear Tienda</h1>

<form action="{{ route('tiendas.store') }}" method="POST">
    @csrf

    <button type="submit">
        Guardar
    </button>
</form>

</body>
</html>