<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Services\CategoriaService;


class CategoriaController extends Controller
{
    private CategoriaService $categoriaService;
    public function __construct(CategoriaService $categoriaService)
    {
        $this->categoriaService = $categoriaService;
    }

    public function index()
    {
        $categorias = $this->categoriaService->listarTodo();
        return view ('Categoria.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Categoria.crear');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoriaRequest $request)
    {
        Categoria::create($request->validated());
        return redirect()->route('categoria.index')->with('success', 'Categoria creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $categoria = $this->categoriaService->buscarPorId($id);
        return view('Categoria.editar', compact('categoria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id, UpdateCategoriaRequest $request)
    {
        $this->categoriaService->actualizar($id, $request->validated());
        return redirect()->route('categoria.index')->with('success', 'Categoria actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->categoriaService->eliminar($id);
        return redirect()->route('categoria.index')->with('success', 'Categoria eliminada correctamente');
    }
}
