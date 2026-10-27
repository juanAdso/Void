<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Services\CategoriaService;

class CategoriaController extends Controller
{
    public function __construct(private CategoriaService $categoriaService) {}

    public function index()
    {
        $categorias = $this->categoriaService->listarTodo();
        return view('categoria.index', compact('categorias'));
    }

    public function create()
    {
        return view('categoria.crear');
    }

    public function store(StoreCategoriaRequest $request)
    {
        $this->categoriaService->guardar($request->validated());
        return redirect()->route('categoria.index')->with('success', 'Categoría creada correctamente.');
    }

    public function edit(int $id)
    {
        $categoria = $this->categoriaService->buscarPorId($id);
        return view('categoria.editar', compact('categoria'));
    }

    public function update(int $id, UpdateCategoriaRequest $request)
    {
        $this->categoriaService->actualizar($id, $request->validated());
        return redirect()->route('categoria.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(int $id)
    {
        if (!$this->categoriaService->eliminar($id)) {
            return redirect()->route('categoria.index')->with('error', 'No se puede eliminar una categoría que tiene productos asociados.');
        }

        return redirect()->route('categoria.index')->with('success', 'Categoría eliminada correctamente.');
    }
}
