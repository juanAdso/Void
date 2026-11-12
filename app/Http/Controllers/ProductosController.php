<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductosRequest;
use App\Http\Requests\UpdateProductosRequest;
use App\Services\CategoriaService;
use App\Services\ProductosService;

class ProductosController extends Controller
{
    public function __construct(
        private ProductosService $productosService,
        private CategoriaService $categoriasService
    ) {}

    public function index()
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $productos = $this->productosService->listarTodo($tiendaId);

        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $categorias = $this->categoriasService->listarTodo($tiendaId);

        return view('productos.crear', compact('categorias'));
    }

    public function store(StoreProductosRequest $request)
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $this->productosService->guardar($request->validated(), $tiendaId);

        return redirect()->route('productos.index')->with('success', 'Producto creado en tu tienda.');
    }

    public function edit(int $id)
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $producto = $this->productosService->buscarPorId($id, $tiendaId);
        $categorias = $this->categoriasService->listarTodo($tiendaId);

        return view('productos.editar', ['productos' => $producto, 'categorias' => $categorias]);
    }

    public function update(int $id, UpdateProductosRequest $request)
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $datos = $request->validated();
        unset($datos['id_tienda']);

        $this->productosService->actualizar($id, $datos, $tiendaId);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(int $id)
    {
        $tiendaId = (int) auth()->user()->id_tienda;

        if (! $this->productosService->eliminar($id, $tiendaId)) {
            return redirect()->route('productos.index')->with('error', 'No se puede eliminar este producto porque está asociado a una factura.');
        }

        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
    }
}
