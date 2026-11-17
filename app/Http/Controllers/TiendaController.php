<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTiendaRequest;
use App\Services\ImagenesService;
use App\Services\TiendaService;
use Illuminate\Support\Facades\Auth;
use Throwable;

class TiendaController extends Controller
{
    public function __construct(
        private TiendaService $tiendaService,
        private ImagenesService $imagenesService
    ) {}

    public function index()
    {
        $tiendaId = (int) Auth::user()?->id_tienda;
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
        $tiendaId = (int) Auth::user()?->id_tienda;
        $tienda = $this->tiendaService->buscarPorId($id, $tiendaId);

        return view('tiendas.show', compact('tienda'));
    }

    public function edit(int $id)
    {
        $tiendaId = (int) Auth::user()?->id_tienda;
        $tienda = $this->tiendaService->buscarPorId($id, $tiendaId);

        return view('tiendas.edit', compact('tienda'));
    }

    public function update(int $id, UpdateTiendaRequest $request)
    {
        $tiendaId = (int) Auth::user()?->id_tienda;
        $tienda = $this->tiendaService->buscarPorId($id, $tiendaId);

        $datos = $request->validated();
        $logoAnterior = $tienda->logo;
        $rutaNueva = null;
        $eliminarLogo = (bool) ($datos['eliminar_logo'] ?? false);

        unset($datos['eliminar_logo'], $datos['logo'], $datos['id_tienda']);

        try {
            if ($request->hasFile('logo')) {
                $rutaNueva = $this->imagenesService->guardarLogo($request->file('logo'), $tiendaId);
                $datos['logo'] = $rutaNueva;
            } elseif ($eliminarLogo) {
                $datos['logo'] = null;
            }

            $this->tiendaService->actualizar($id, $datos, $tiendaId);
        } catch (Throwable $exception) {
            $this->imagenesService->eliminarLogo($rutaNueva, $tiendaId);
            throw $exception;
        }

        if (($rutaNueva || $eliminarLogo) && $logoAnterior) {
            $this->imagenesService->eliminarLogo($logoAnterior, $tiendaId);
        }

        return redirect()->route('tiendas.index')->with('success', 'Tienda actualizada correctamente.');
    }

    public function destroy(int $id)
    {
        abort(403, 'No se permite eliminar una tienda desde su panel. Utiliza el proceso de desactivación.');
    }
}
