<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTiendaRequest;
use App\Services\TiendaService;
use Illuminate\Support\Collection;

class TiendaController extends Controller
{
    public function __construct(private TiendaService $tiendaService) {}

    public function index()
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $tiendas = collect([$this->tiendaService->buscarPorId($tiendaId)]);

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

        abort_unless($id === $tiendaId, 404);

        $tienda = $this->tiendaService->buscarPorId($tiendaId);

        return view('tiendas.show', compact('tienda'));
    }

    public function edit(int $id)
    {
        $tiendaId = (int) auth()->user()->id_tienda;

        abort_unless($id === $tiendaId, 404);

        $tienda = $this->tiendaService->buscarPorId($tiendaId);

        return view('tiendas.edit', compact('tienda'));
    }

    public function update(int $id, UpdateTiendaRequest $request)
    {
        $tiendaId = (int) auth()->user()->id_tienda;

        abort_unless($id === $tiendaId, 404);

        $datos = $request->validated();
        unset($datos['id_tienda']);

        $this->tiendaService->actualizar($tiendaId, $datos);

        return redirect()->route('tiendas.index')->with('success', 'Tienda actualizada correctamente.');
    }

    public function destroy(int $id)
    {
        abort(403, 'No se permite eliminar una tienda desde su panel. Utiliza el proceso de desactivación.');
    }
}
