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
        $tiendaId = (int) auth()->user()->id_tienda;
        $clientes = $this->clienteService->listarTodo($tiendaId);

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        $this->authorize('create', Cliente::class);

        return view('clientes.crear');
    }

    public function store(StoreClienteRequest $request)
    {
        $this->authorize('create', Cliente::class);
        $this->clienteService->guardar($request->validated(), (int) auth()->user()->id_tienda);

        return redirect()->route('cliente.index')->with('success', 'Cliente creado correctamente.');
    }

    public function edit(int $id)
    {
        $cliente = $this->clienteService->buscarPorId($id, (int) auth()->user()->id_tienda);
        $this->authorize('update', $cliente);

        return view('clientes.editar', compact('cliente'));
    }

    public function update(int $id, UpdateClienteRequest $request)
    {
        $cliente = $this->clienteService->buscarPorId($id, (int) auth()->user()->id_tienda);
        $this->authorize('update', $cliente);
        $this->clienteService->actualizar($id, $request->validated(), (int) auth()->user()->id_tienda);

        return redirect()->route('cliente.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(int $id)
    {
        $cliente = $this->clienteService->buscarPorId($id, (int) auth()->user()->id_tienda);
        $this->authorize('delete', $cliente);

        if (! $this->clienteService->eliminar($id, (int) auth()->user()->id_tienda)) {
            return redirect()->route('cliente.index')->with('error', 'No se puede eliminar un cliente que tiene facturas asociadas.');
        }

        return redirect()->route('cliente.index')->with('success', 'Cliente eliminado correctamente.');
    }
}
