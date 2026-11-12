<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTiendaRequest;
use App\Services\TiendaService;

class TiendaController extends Controller
{
    public function __construct(private TiendaService $tiendaService) {}

    public function index()
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $tiendas = $this->tiendaService->listarTodo($tiendaId);

        return view('tiendas.index', compact('tiendas'));
    }

    public function create()
    {
        abort(403, 'Las nuevas tiendas deben registrarse mediante el formulario público de registro.');
    }

    public function store()
    {
        abort(403, 'Las nuevas tiendas deben registrarse mediante el formulario público de registro.');
    }

    public function show(int $id)
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $tienda = $this->tiendaService->buscarPorId($id, $tiendaId);

        return view('tiendas.show', compact('tienda'));
    }

    public function edit(int $id)
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $tienda = $this->tiendaService->buscarPorId($id, $tiendaId);

        return view('tiendas.edit', compact('tienda'));
    }

    public function update(int $id, UpdateTiendaRequest $request)
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $datos = $request->validated();
        unset($datos['id_tienda']);

        $this->tiendaService->actualizar($id, $datos, $tiendaId);

        return redirect()->route('tiendas.index')->with('success', 'Tienda actualizada correctamente.');
    }

    public function destroy(int $id)
    {
        abort(403, 'No se permite eliminar una tienda desde su panel. Utiliza el proceso de desactivación.');
    }
}
