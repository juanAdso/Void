<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDetalleFacturaRequest;
use App\Http\Requests\UpdateDetalleFacturaRequest;
use App\Services\DetalleFacturaService;

class DetalleFacturaController extends Controller
{
    public function __construct(private DetalleFacturaService $detalleFacturaService) {}

    public function store(StoreDetalleFacturaRequest $request)
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $detalle = $this->detalleFacturaService->guardar($request->validated(), $tiendaId);

        return redirect()->route('facturas.show', $detalle->id_factura)
            ->with('success', 'Producto agregado a la factura. El inventario y los totales se actualizaron.');
    }

    public function update(int $id, UpdateDetalleFacturaRequest $request)
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $detalle = $this->detalleFacturaService->actualizar($id, $request->validated(), $tiendaId);

        return redirect()->route('facturas.show', $detalle->id_factura)
            ->with('success', 'Producto de la factura actualizado. El inventario y los totales se recalcularon.');
    }

    public function destroy(int $id)
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $detalle = $this->detalleFacturaService->buscarPorId($id, $tiendaId);
        $facturaId = (int) $detalle->id_factura;

        $this->detalleFacturaService->eliminar($id, $tiendaId);

        return redirect()->route('facturas.show', $facturaId)
            ->with('success', 'Producto retirado de la factura. Las unidades regresaron al inventario.');
    }
}
