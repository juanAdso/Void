<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Factura #{{ $factura->id }} - VØID</title>
    <style>
        @page {
            margin: 28px 32px;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            color: #202938;
            font-size: 10px;
            line-height: 1.45;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .brand {
            color: #111827;
            font-size: 23px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .brand span {
            color: #2563eb;
        }

        .muted {
            color: #64748b;
        }

        .invoice {
            text-align: right;
        }

        .invoice h1 {
            margin: 0 0 5px;
            font-size: 19px;
        }

        .status {
            display: inline-block;
            margin-top: 5px;
            padding: 4px 8px;
            border-radius: 10px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .section-title {
            margin: 18px 0 8px;
            color: #1e3a8a;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .details,
        .items,
        .totals {
            width: 100%;
            border-collapse: collapse;
        }

        .details td {
            width: 50%;
            padding: 7px 9px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .label {
            display: block;
            margin-bottom: 3px;
            color: #64748b;
            font-size: 8px;
            text-transform: uppercase;
        }

        .items {
            margin-top: 8px;
        }

        .items th {
            padding: 8px;
            border-bottom: 1px solid #cbd5e1;
            background: #eff4fb;
            color: #1e293b;
            font-size: 8px;
            text-align: left;
            text-transform: uppercase;
        }

        .items td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        .right {
            text-align: right;
        }

        .totals {
            width: 48%;
            margin: 14px 0 0 auto;
        }

        .totals td {
            padding: 5px 7px;
            border-bottom: 1px solid #e2e8f0;
        }

        .grand-total td {
            border-top: 2px solid #2563eb;
            border-bottom: 0;
            color: #1d4ed8;
            font-size: 12px;
            font-weight: bold;
        }

        .footer {
            margin-top: 32px;
            padding-top: 9px;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 8px;
            text-align: center;
        }
    </style>
</head>

<body>
    @php
        $nombreCliente = trim(($factura->cliente?->nombre ?? '') . ' ' . ($factura->cliente?->apellido ?? ''));
        $nombreResponsable = trim(($factura->usuario?->nombre ?? '') . ' ' . ($factura->usuario?->apellido ?? ''));
    @endphp

    <table class="header">
        <tr>
            <td>
                <div class="brand">V<span>Ø</span>ID</div>
                <div class="muted">Gestión comercial, inventario y facturación</div>
            </td>
            <td class="invoice">
                <h1>Factura #{{ $factura->id }}</h1>
                <div class="muted">
                    Fecha de emisión: {{ $factura->fecha?->format('d/m/Y') ?? 'Sin fecha' }}
                </div>
                <div class="status">{{ ucfirst($factura->estado) }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Información de la factura</div>
    <table class="details">
        <tr>
            <td>
                <span class="label">Cliente</span>
                <strong>{{ $nombreCliente ?: 'Cliente no disponible' }}</strong><br>
                <span class="muted">
                    Documento: {{ $factura->cliente?->documento ?? 'No registrado' }}
                </span>
            </td>
            <td>
                <span class="label">Responsable</span>
                <strong>{{ $nombreResponsable ?: 'Usuario no disponible' }}</strong><br>
                <span class="muted">
                    Tienda: {{ $factura->usuario?->tienda?->nombre ?? 'No disponible' }}
                </span>
            </td>
        </tr>
    </table>

    <div class="section-title">Productos facturados</div>
    <table class="items">
        <thead>
            <tr>
                <th>Producto</th>
                <th class="right">Cantidad</th>
                <th class="right">Precio unitario</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($factura->detalle_factura as $detalle)
                <tr>
                    <td>{{ $detalle->producto?->nombre ?? 'Producto no disponible' }}</td>
                    <td class="right">{{ $detalle->cantidad }}</td>
                    <td class="right">
                        $ {{ number_format((float) $detalle->precio_unitario, 2, ',', '.') }}
                    </td>
                    <td class="right">
                        $ {{ number_format((float) $detalle->subtotal, 2, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="muted">
                        Esta factura no contiene productos asociados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td class="right">
                $ {{ number_format((float) $factura->subtotal, 2, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td>IVA (19 %)</td>
            <td class="right">
                $ {{ number_format((float) $factura->iva, 2, ',', '.') }}
            </td>
        </tr>
        <tr class="grand-total">
            <td>Total</td>
            <td class="right">
                $ {{ number_format((float) $factura->total, 2, ',', '.') }}
            </td>
        </tr>
    </table>

    <div class="footer">
        Documento generado por VØID · Conserve este comprobante para sus registros.
    </div>
</body>

</html>
