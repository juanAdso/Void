<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use App\Services\ClienteService;

class ClienteController extends Controller
{
    public function __construct(private ClienteService $clienteService) {}

    public function index()
    {
        $this->authorize('viewAny', Cliente::class);
        $tiendaId = auth()->user()->alcanceTiendaId();
        $clientes = $this->clienteService->listarTodo($tiendaId);

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        $this->authorize('create', Cliente::class);
        auth()->user()->requiereTiendaId();

        return view('clientes.crear');
    }

    public function store(StoreClienteRequest $request)
    {
        $this->authorize('create', Cliente::class);
        $this->clienteService->guardar($request->validated(), auth()->user()->requiereTiendaId());

        return redirect()->route('cliente.index')->with('success', 'Cliente creado correctamente.');
    }

    public function edit(int $id)
    {
        $cliente = $this->clienteService->buscarPorId($id, auth()->user()->alcanceTiendaId());
        $this->authorize('update', $cliente);

        return view('clientes.editar', compact('cliente'));
    }

    public function update(int $id, UpdateClienteRequest $request)
    {
        $tiendaId = auth()->user()->alcanceTiendaId();
        $cliente = $this->clienteService->buscarPorId($id, $tiendaId);
        $this->authorize('update', $cliente);
        $this->clienteService->actualizar($id, $request->validated(), $tiendaId);

        return redirect()->route('cliente.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(int $id)
    {
        $tiendaId = auth()->user()->alcanceTiendaId();
        $cliente = $this->clienteService->buscarPorId($id, $tiendaId);
        $this->authorize('delete', $cliente);

        if (! $this->clienteService->eliminar($id, $tiendaId)) {
            return redirect()->route('cliente.index')->with('error', 'No se puede eliminar un cliente que tiene facturas asociadas.');
        }

        return redirect()->route('cliente.index')->with('success', 'Cliente eliminado correctamente.');
    }
}
