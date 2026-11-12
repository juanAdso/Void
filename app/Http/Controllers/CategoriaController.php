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
        $tiendaId = (int) auth()->user()->id_tienda;
        $categorias = $this->categoriaService->listarTodo($tiendaId);

        return view('categoria.index', compact('categorias'));
    }

    public function create()
    {
        $this->authorize('create', Categoria::class);

        return view('categoria.crear');
    }

    public function store(StoreCategoriaRequest $request)
    {
        $this->authorize('create', Categoria::class);
        $this->categoriaService->guardar($request->validated(), (int) auth()->user()->id_tienda);

        return redirect()->route('categoria.index')->with('success', 'Categoría creada correctamente.');
    }

    public function edit(int $id)
    {
        $categoria = $this->categoriaService->buscarPorId($id, (int) auth()->user()->id_tienda);
        $this->authorize('update', $categoria);

        return view('categoria.editar', compact('categoria'));
    }

    public function update(int $id, UpdateCategoriaRequest $request)
    {
        $categoria = $this->categoriaService->buscarPorId($id, (int) auth()->user()->id_tienda);
        $this->authorize('update', $categoria);
        $this->categoriaService->actualizar($id, $request->validated(), (int) auth()->user()->id_tienda);

        return redirect()->route('categoria.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(int $id)
    {
        $categoria = $this->categoriaService->buscarPorId($id, (int) auth()->user()->id_tienda);
        $this->authorize('delete', $categoria);

        if (! $this->categoriaService->eliminar($id, (int) auth()->user()->id_tienda)) {
            return redirect()->route('categoria.index')->with('error', 'No se puede eliminar una categoría que tiene productos asociados.');
        }

        return redirect()->route('categoria.index')->with('success', 'Categoría eliminada correctamente.');
    }
}
