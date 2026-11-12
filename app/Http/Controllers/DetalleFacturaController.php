<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDetalleFacturaRequest;
use App\Http\Requests\UpdateDetalleFacturaRequest;
use App\Services\DetalleFacturaService;
use App\Services\FacturasService;
use App\Services\ProductosService;

class DetalleFacturaController extends Controller
{
    public function __construct(
        private DetalleFacturaService $detallefacturaservice,
        private ProductosService $productoService,
        private FacturasService $facturaservice
    ) {}

    public function index()
    {
        $detalle_facturas = $this->detallefacturaservice->listarTodo(auth()->user()->alcanceTiendaId());

        return view('detalle_facturas.index', compact('detalle_facturas'));
    }

    public function create()
    {
        $tiendaId = auth()->user()->requiereTiendaId();
        $productos = $this->productoService->listarTodo($tiendaId);
        $facturas = $this->facturaservice->listarTodo($tiendaId);

        return view('detalle_facturas.create', compact('productos', 'facturas'));
    }

    public function store(StoreDetalleFacturaRequest $request)
    {
        $this->detallefacturaservice->guardar($request->validated(), auth()->user()->requiereTiendaId());

        return redirect()->route('detalle_facturas.index')->with('success', 'Detalle creado correctamente. El inventario y los totales se actualizaron.');
    }

    public function edit(int $id)
    {
        $tiendaId = auth()->user()->alcanceTiendaId();
        $detalle_factura = $this->detallefacturaservice->buscarPorId($id, $tiendaId);
        $tiendaFacturaId = $tiendaId ?? (int) $detalle_factura->factura->usuario->id_tienda;
        $productos = $this->productoService->listarTodo($tiendaFacturaId);
        $facturas = $this->facturaservice->listarTodo($tiendaFacturaId);

        return view('detalle_facturas.edit', compact('detalle_factura', 'productos', 'facturas'));
    }

    public function update(int $id, UpdateDetalleFacturaRequest $request)
    {
        $this->detallefacturaservice->actualizar($id, $request->validated(), auth()->user()->alcanceTiendaId());

        return redirect()->route('detalle_facturas.index')->with('success', 'Detalle actualizado correctamente. El inventario y los totales se actualizaron.');
    }

    public function destroy(int $id)
    {
        $this->detallefacturaservice->eliminar($id, auth()->user()->alcanceTiendaId());

        return redirect()->route('detalle_facturas.index')->with('success', 'Detalle eliminado correctamente. El inventario y los totales se actualizaron.');
    }
}
