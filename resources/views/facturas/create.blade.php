<!DOCTYPE html>
<html>
<head>
    <title>Crear Facturas</title>
</head>
<body>

<h1>Crear Facturas</h1>

<form action="{{ route('facturas.store') }}" method="POST">
    @csrf

    <button type="submit">
        Guardar
    </button>
</form>

</body>
</html>