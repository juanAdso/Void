<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacturasRequest;
use App\Http\Requests\UpdateFacturasRequest;
use App\Services\FacturasService;
use App\Services\ClienteService;

class FacturasController extends Controller
{
    public function __construct(
        private FacturasService $facturasService,
        private ClienteService $clientesService
    ) {}

    public function index()
    {
        $facturas = $this->facturasService->listarTodo();
        return view('facturas.index', compact('facturas'));
    }

    public function create()
    {
        $clientes = $this->clientesService->listarTodo();
        return view('facturas.create', compact('clientes'));
    }

    public function store(StoreFacturasRequest $request)
    {
        $this->facturasService->guardar($request->validated());
        return redirect()->route('facturas.index')->with('success', 'Factura creada correctamente.');
    }

    public function edit(int $id)
    {
        $facturas = $this->facturasService->buscarPorId($id);
        $clientes = $this->clientesService->listarTodo();
        return view('facturas.edit', compact('facturas', 'clientes'));
    }

    public function update(int $id, UpdateFacturasRequest $request)
    {
        $this->facturasService->actualizar($id, $request->validated());
        return redirect()->route('facturas.index')->with('success', 'Factura actualizada correctamente.');
    }

    public function destroy(int $id)
    {
        if (!$this->facturasService->eliminar($id)) {
            return redirect()->route('facturas.index')->with('error', 'No se puede eliminar una factura que tiene detalles asociados. Elimina primero sus detalles.');
        }

        return redirect()->route('facturas.index')->with('success', 'Factura eliminada correctamente.');
    }
}
