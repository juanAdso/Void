<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Models\Categoria;
use App\Services\CategoriaService;

class CategoriaController extends Controller
{
    public function __construct(private CategoriaService $categoriaService) {}

    public function index()
    {
        $this->authorize('viewAny', Categoria::class);
        $tiendaId = auth()->user()->alcanceTiendaId();
        $categorias = $this->categoriaService->listarTodo($tiendaId);

        return view('categoria.index', compact('categorias'));
    }

    public function create()
    {
        $this->authorize('create', Categoria::class);
        auth()->user()->requiereTiendaId();

        return view('categoria.crear');
    }

    public function store(StoreCategoriaRequest $request)
    {
        $this->authorize('create', Categoria::class);
        $this->categoriaService->guardar($request->validated(), auth()->user()->requiereTiendaId());

        return redirect()->route('categoria.index')->with('success', 'Categoría creada correctamente.');
    }

    public function edit(int $id)
    {
        $categoria = $this->categoriaService->buscarPorId($id, auth()->user()->alcanceTiendaId());
        $this->authorize('update', $categoria);

        return view('categoria.editar', compact('categoria'));
    }

    public function update(int $id, UpdateCategoriaRequest $request)
    {
        $tiendaId = auth()->user()->alcanceTiendaId();
        $categoria = $this->categoriaService->buscarPorId($id, $tiendaId);
        $this->authorize('update', $categoria);
        $this->categoriaService->actualizar($id, $request->validated(), $tiendaId);

        return redirect()->route('categoria.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(int $id)
    {
        $tiendaId = auth()->user()->alcanceTiendaId();
        $categoria = $this->categoriaService->buscarPorId($id, $tiendaId);
        $this->authorize('delete', $categoria);

        if (! $this->categoriaService->eliminar($id, $tiendaId)) {
            return redirect()->route('categoria.index')->with('error', 'No se puede eliminar una categoría que tiene productos asociados.');
        }

        return redirect()->route('categoria.index')->with('success', 'Categoría eliminada correctamente.');
    }
}
