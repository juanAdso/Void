<!DOCTYPE html>
<html>
<head>
    <title>Crear DetalleFactura</title>
</head>
<body>

<h1>Crear DetalleFactura</h1>

<form action="{{ route('detalle_facturas.store') }}" method="POST">
    @csrf

    <button type="submit">
        Guardar
    </button>
</form>

</body>
</html>