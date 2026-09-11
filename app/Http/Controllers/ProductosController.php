<?php

namespace App\Http\Controllers;

use App\Models\Productos;
use App\Http\Requests\StoreProductosRequest;
use App\Http\Requests\UpdateProductosRequest;
use App\Services\ProductosService;
use App\Services\CategoriaService;


class ProductosController extends Controller
{
    private ProductosService $productosService;
    private CategoriaService $categoriasService;
    public function __construct(ProductosService $productosService, CategoriaService $categoriasService)
    {
        $this->productosService = $productosService;
        $this->categoriasService = $categoriasService;
    }
    public function index()
    {
        $productos = $this->productosService->listarTodo();
        return view('productos.index', compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = $this->categoriasService->listarTodo();
        return view('productos.crear', compact('categorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductosRequest $request)
    {
        $this->productosService->guardar($request->validated());

        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $productos = $this->productosService->buscarPorId($id);
        return view('productos.editar', compact('productos'));
    }
    

  
    public function update(int $id, UpdateProductosRequest $request)
    {
        $this->productosService->actualizar($id, $request->validated());
        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->productosService->eliminar($id);
        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente');
    }
}
