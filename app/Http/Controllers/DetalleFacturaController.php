<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDetalleFacturaRequest;
use App\Http\Requests\UpdateDetalleFacturaRequest;
use App\Services\DetalleFacturaService;
use App\Services\FacturasService;
use App\Services\ProductosService;

class DetalleFacturaController extends Controller
{
    private DetalleFacturaService $detallefacturaservice;

    private ProductosService $productoService;

    private FacturasService $facturaservice;

    public function __construct(DetalleFacturaService $detallefacturaservice, ProductosService $productoService, FacturasService $facturaservice)
    {
        $this->detallefacturaservice = $detallefacturaservice;
        $this->productoService = $productoService;
        $this->facturaservice = $facturaservice;
    }

    public function index()
    {
        $detalle_facturas = $this->detallefacturaservice->listarTodo();

        return view('detalle_facturas.index', compact('detalle_facturas'));
    }

    public function create()
    {
        $productos = $this->productoService->listarTodo();
        $facturas = $this->facturaservice->listarTodo();

        return view('detalle_facturas.create', compact('productos', 'facturas'));
    }

    public function store(StoreDetalleFacturaRequest $request)
    {
        $this->detallefacturaservice->guardar($request->validated());

        return redirect()->route('detalle_facturas.index');
    }

    public function show()
    {
        //
    }

    public function edit(int $id)
    {
        $detalle_factura = $this->detallefacturaservice->buscarPorId($id);

        $productos = $this->productoService->listarTodo();
        $facturas = $this->facturaservice->listarTodo();

        return view('detalle_facturas.edit', compact('detalle_factura', 'productos', 'facturas'));
    }

    public function update(int $id, UpdateDetalleFacturaRequest $request)
    {
        $this->detallefacturaservice->actualizar($id, $request->validated());

        return redirect()->route('detalle_facturas.index');
    }

    public function destroy(int $id)
    {
        $this->detallefacturaservice->eliminar($id);

        return redirect()->route('detalle_facturas.index');
    }
}
