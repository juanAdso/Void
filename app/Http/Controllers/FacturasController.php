<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacturasRequest;
use App\Http\Requests\UpdateFacturasRequest;
use App\Services\ClienteService;
use App\Services\FacturasService;

class FacturasController extends Controller
{
    public function __construct(
        private FacturasService $facturasService,
        private ClienteService $clientesService
    ) {}

    public function index()
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $facturas = $this->facturasService->listarTodo($tiendaId);

        return view('facturas.index', compact('facturas'));
    }

    public function create()
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $clientes = $this->clientesService->listarTodo($tiendaId);

        return view('facturas.create', compact('clientes'));
    }

    public function store(StoreFacturasRequest $request)
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $datos = $request->validated();
        $datos['id_usuario'] = auth()->id();
        $datos['subtotal'] = 0;
        $datos['iva'] = 0;
        $datos['total'] = 0;

        $this->facturasService->guardar($datos, $tiendaId);

        return redirect()->route('facturas.index')->with('success', 'Factura creada. Ahora agrega sus productos desde Detalle de facturas.');
    }

    public function edit(int $id)
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $facturas = $this->facturasService->buscarPorId($id, $tiendaId);
        $clientes = $this->clientesService->listarTodo($tiendaId);

        return view('facturas.edit', compact('facturas', 'clientes'));
    }

    public function update(int $id, UpdateFacturasRequest $request)
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $datos = $request->validated();
        unset($datos['id_usuario'], $datos['id_tienda']);

        $this->facturasService->actualizar($id, $datos, $tiendaId);

        return redirect()->route('facturas.index')->with('success', 'Factura actualizada correctamente.');
    }

    public function destroy(int $id)
    {
        $tiendaId = (int) auth()->user()->id_tienda;

        if (! $this->facturasService->eliminar($id, $tiendaId)) {
            return redirect()->route('facturas.index')->with('error', 'No se puede eliminar la factura. Solo se pueden eliminar facturas en estado pendiente y sin detalles asociados.');
        }

        return redirect()->route('facturas.index')->with('success', 'Factura eliminada correctamente.');
    }
}
