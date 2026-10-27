<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Services\ClienteService;

class ClienteController extends Controller
{
    public function __construct(private ClienteService $clienteService) {}

    public function index()
    {
        $clientes = $this->clienteService->listarTodo();
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.crear');
    }

    public function store(StoreClienteRequest $request)
    {
        $this->clienteService->guardar($request->validated());
        return redirect()->route('cliente.index')->with('success', 'Cliente creado correctamente.');
    }

    public function edit(int $id)
    {
        $cliente = $this->clienteService->buscarPorId($id);
        return view('clientes.editar', compact('cliente'));
    }

    public function update(int $id, UpdateClienteRequest $request)
    {
        $this->clienteService->actualizar($id, $request->validated());
        return redirect()->route('cliente.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(int $id)
    {
        if (!$this->clienteService->eliminar($id)) {
            return redirect()->route('cliente.index')->with('error', 'No se puede eliminar un cliente que tiene facturas asociadas.');
        }

        return redirect()->route('cliente.index')->with('success', 'Cliente eliminado correctamente.');
    }
}
