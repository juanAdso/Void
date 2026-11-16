<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use App\Services\ClienteService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ClienteController extends Controller
{
    public function __construct(private ClienteService $clienteService) {}

    public function index()
    {
        $this->authorize('viewAny', Cliente::class);
        $tiendaId = (int) (Auth::user()?->id_tienda ?? 0);
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
        $this->clienteService->guardar($request->validated(), (int) (Auth::user()?->id_tienda ?? 0));

        return redirect()->route('cliente.index')->with('success', 'Cliente creado correctamente.');
    }

    public function edit(int $id)
    {
        $tiendaId = (int) (Auth::user()?->id_tienda ?? 0);
        $cliente = $this->clienteService->buscarPorId($id, $tiendaId);
        $this->authorize('update', $cliente);

        return view('clientes.editar', compact('cliente'));
    }

    public function update(int $cliente, UpdateClienteRequest $request)
{
    $idTienda = (int) (request()->user()?->id_tienda ?? 0);

    $clienteEncontrado = $this->clienteService->buscarPorId($cliente, $idTienda);

    Gate::authorize('update', $clienteEncontrado);

    $this->clienteService->actualizar(
        $cliente,
        $request->validated(),
        $idTienda
    );

    return redirect()->route('cliente.index')
        ->with('success', 'Cliente actualizado correctamente.');
}

    public function destroy(int $id)
    {
        $tiendaId = (int) (Auth::user()?->id_tienda ?? 0);
        $cliente = $this->clienteService->buscarPorId($id, $tiendaId);
        $this->authorize('delete', $cliente);

        if (! $this->clienteService->eliminar($id, $tiendaId)) {
            return redirect()->route('cliente.index')->with('error', 'No se puede eliminar un cliente que tiene facturas asociadas.');
        }

        return redirect()->route('cliente.index')->with('success', 'Cliente eliminado correctamente.');
    }
}
