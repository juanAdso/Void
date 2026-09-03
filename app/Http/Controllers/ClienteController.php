<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Services\ClienteService;

class ClienteController extends Controller
{
    private ClienteService $clienteService;
    public function __construct(ClienteService $clienteService)
    {
        $this->clienteService = $clienteService;
    }

    public function index()
    {
        $clientes = $this->clienteService->listarTodo();
        return view('cliente.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cliente.crear');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClienteRequest $request)
    {
        Cliente::create($request->validated());
        $clientes = $this->clienteService->listarTodo();
        return redirect()->route('cliente.index')->with('success', 'Cliente creado correctamente');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $cliente = $this->clienteService->buscarPorId($id);
        return view('cliente.editar', compact('cliente  '));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id, UpdateClienteRequest $request)
    {
        $this->clienteService->actualizar($id, $request->validated());
        return redirect()->route('cliente.index')->with('success', 'Cliente actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->clienteService->eliminar($id);
        return redirect()->route('cliente.index')->with('success', 'Cliente eliminado correctamente');
    }
}
