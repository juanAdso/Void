<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacturasRequest;
use App\Http\Requests\UpdateFacturasRequest;
use App\Services\FacturasService;
use App\Services\ClienteService;

   class FacturasController extends Controller
{
    private FacturasService $facturasService;
    private ClienteService $clientesService;
    public function __construct(FacturasService $facturasService, ClienteService $clientesService)
    {
        $this->facturasService = $facturasService;
        $this->clientesService = $clientesService;
    }
    public function index()
    {
        $facturas = $this->facturasService->listarTodo();
        return view('facturas.index', compact('facturas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = $this->clientesService->listarTodo();
        return view('facturas.create', compact('clientes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFacturasRequest $request)
    {
        $this->facturasService->guardar($request->validated());

        return redirect()->route('facturas.index')->with('success', 'Factura creada correctamente');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $facturas = $this->facturasService->buscarPorId($id);
        $clientes = $this->clientesService->listarTodo();
        return view('facturas.edit', compact('facturas','clientes'));
    }
    

  
    public function update(int $id, UpdateFacturasRequest $request)
    {
        $this->facturasService->actualizar($id, $request->validated());
        return redirect()->route('facturas.index')->with('success', 'Factura actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->facturasService->eliminar($id);
        return redirect()->route('facturas.index')->with('success', 'Factura eliminada correctamente');
    }
}
