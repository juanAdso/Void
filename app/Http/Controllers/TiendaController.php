<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTiendaRequest;
use App\Http\Requests\UpdateTiendaRequest;
use App\Services\TiendaService;

class TiendaController extends Controller
{
    private TiendaService $tiendaService;

    public function __construct(TiendaService $tiendaService)
    {
        $this->tiendaService = $tiendaService;
    }

    public function index()
    {
        $tiendas = $this->tiendaService->listarTodo();
        return view('tiendas.index', compact('tiendas'));
    }

    public function create()
    {
        return view('tiendas.create');
    }

    public function store(StoreTiendaRequest $request)
    {
        $this->tiendaService->guardar($request->validated());
        return redirect()->route('tiendas.index')->with('success', 'Tienda creada correctamente.');
    }

    public function show(int $id)
    {
        $tienda = $this->tiendaService->buscarPorId($id);
        return view('tiendas.show', compact('tienda'));
    }

    public function edit(int $id)
    {
        $tienda = $this->tiendaService->buscarPorId($id);
        return view('tiendas.edit', compact('tienda'));
    }

    public function update(int $id, UpdateTiendaRequest $request)
    {
        $this->tiendaService->actualizar($id, $request->validated());
        return redirect()->route('tiendas.index')->with('success', 'Tienda actualizada correctamente.');
    }

    public function destroy(int $id)
    {
        $this->tiendaService->eliminar($id);
        return redirect()->route('tiendas.index')->with('success', 'Tienda eliminada correctamente.');
    }
}
