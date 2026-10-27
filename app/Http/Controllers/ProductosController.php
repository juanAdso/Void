<?php

namespace App\Http\Controllers;

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

    public function create()
    {
        $categorias = $this->categoriasService->listarTodo();
        return view('productos.crear', compact('categorias'));
    }

    public function store(StoreProductosRequest $request)
    {
        $this->productosService->guardar($request->validated());

        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente');
    }

    public function edit(int $id)
    {
        $productos = $this->productosService->buscarPorId($id);
        $categorias = $this->categoriasService->listarTodo();
        return view('productos.editar', compact('productos', 'categorias'));
    }

    public function update(int $id, UpdateProductosRequest $request)
    {
        $this->productosService->actualizar($id, $request->validated());
        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente');
    }

    public function destroy(int $id)
    {
        $eliminado = $this->productosService->eliminar($id);

        if (!$eliminado) {
            return redirect()->route('productos.index')->with(
                'error',
                'No se puede eliminar este producto porque está asociado a una factura.'
            );
        }

        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente');
    }
}
