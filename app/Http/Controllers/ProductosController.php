<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductosRequest;
use App\Http\Requests\UpdateProductosRequest;
use App\Models\Productos;
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
        $this->authorize('viewAny', Productos::class);
        $tiendaId = auth()->user()->alcanceTiendaId();
        $productos = $this->productosService->listarTodo($tiendaId);

        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $this->authorize('create', Productos::class);
        $tiendaId = auth()->user()->requiereTiendaId();
        $categorias = $this->categoriasService->listarTodo($tiendaId);

        return view('productos.crear', compact('categorias'));
    }

    public function store(StoreProductosRequest $request)
    {
        $this->authorize('create', Productos::class);
        $tiendaId = auth()->user()->requiereTiendaId();
        $this->productosService->guardar($request->validated(), $tiendaId);

        return redirect()->route('productos.index')->with('success', 'Producto creado en tu tienda.');
    }

    public function edit(int $id)
    {
        $tiendaId = auth()->user()->alcanceTiendaId();
        $producto = $this->productosService->buscarPorId($id, $tiendaId);
        $this->authorize('update', $producto);
        $tiendaProductoId = $tiendaId ?? (int) $producto->id_tienda;
        $categorias = $this->categoriasService->listarTodo($tiendaProductoId);

        return view('productos.editar', ['productos' => $producto, 'categorias' => $categorias]);
    }

    public function update(int $id, UpdateProductosRequest $request)
    {
        $tiendaId = auth()->user()->alcanceTiendaId();
        $producto = $this->productosService->buscarPorId($id, $tiendaId);
        $this->authorize('update', $producto);

        $datos = $request->validated();
        unset($datos['id_tienda']);

        $this->productosService->actualizar($id, $datos, $tiendaId);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(int $id)
    {
        $tiendaId = auth()->user()->alcanceTiendaId();
        $producto = $this->productosService->buscarPorId($id, $tiendaId);
        $this->authorize('delete', $producto);

        if (! $this->productosService->eliminar($id, $tiendaId)) {
            return redirect()->route('productos.index')->with('error', 'No se puede eliminar este producto porque está asociado a una factura.');
        }

        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
    }
}
