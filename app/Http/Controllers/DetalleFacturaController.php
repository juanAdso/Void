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
        $tiendaId = auth()->user()?->id_tienda;
        $detalle_facturas = $this->detallefacturaservice->listarTodo($tiendaId);

        return view('detalle_facturas.index', compact('detalle_facturas'));
    }

    public function create()
    {
        $tiendaId = auth()->user()?->id_tienda;
        $productos = $this->productoService->listarTodo($tiendaId);
        $facturas = $this->facturaservice->listarTodo($tiendaId);

        return view('detalle_facturas.create', compact('productos', 'facturas'));
    }

    public function store(StoreDetalleFacturaRequest $request)
    {
        $tiendaId = auth()->user()?->id_tienda;
        if (! $tiendaId) {
            abort(403, 'No tienes una tienda asignada.');
        }

        $this->detallefacturaservice->guardar($request->validated());

        return redirect()->route('detalle_facturas.index')->with('success', 'Detalle creado correctamente. El inventario y los totales se actualizaron.');
    }

    public function edit(int $id)
    {
        $tiendaId = auth()->user()?->id_tienda;
        $detalle_factura = $this->detallefacturaservice->buscarPorId($id, $tiendaId);
        $productos = $this->productoService->listarTodo($tiendaId);
        $facturas = $this->facturaservice->listarTodo($tiendaId);

        return view('detalle_facturas.edit', compact('detalle_factura', 'productos', 'facturas'));
    }

    public function update(int $id, UpdateDetalleFacturaRequest $request)
    {
        $tiendaId = auth()->user()?->id_tienda;
        // Validar que el detalle actual pertenezca a la tienda
        $this->detallefacturaservice->buscarPorId($id, $tiendaId);

        $this->detallefacturaservice->actualizar($id, $request->validated());

        return redirect()->route('detalle_facturas.index')->with('success', 'Detalle actualizado correctamente. El inventario y los totales se actualizaron.');
    }

    public function destroy(int $id)
    {
        $tiendaId = auth()->user()?->id_tienda;
        $this->detallefacturaservice->eliminar($id, $tiendaId);

        return redirect()->route('detalle_facturas.index')->with('success', 'Detalle eliminado correctamente. El inventario y los totales se actualizaron.');
    }
}
