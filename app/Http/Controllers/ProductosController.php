<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductosRequest;
use App\Http\Requests\UpdateProductosRequest;
use App\Services\ProductosService;
use App\Services\CategoriaService;
use App\Models\Productos;
use App\Services\TiendaService;

class ProductosController extends Controller
{
    private ProductosService $productosService;
    private CategoriaService $categoriasService;
    private TiendaService $tiendasService;

    public function __construct(
        ProductosService $productosService,
        CategoriaService $categoriasService,
        TiendaService $tiendasService
    ) {
        $this->productosService = $productosService;
        $this->categoriasService = $categoriasService;
        $this->tiendasService = $tiendasService;
    }

    public function index()
    {
        /** @var \App\Models\User $usuario */
        $usuario = auth()->user();
        $tiendaId = $usuario ? $usuario->id_tienda : null;

        // Solo lista los productos que pertenecen a la tienda del usuario
        $productos = Productos::query()
            ->when($tiendaId, fn($q) => $q->where('id_tienda', $tiendaId))
            ->get();

        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $categorias = $this->categoriasService->listarTodo();
        $tiendas = $this->tiendasService->listarTodo();
        return view('productos.crear', compact('categorias', 'tiendas'));
    }

    public function store(StoreProductosRequest $request)
    {
        $datos = $request->validated();

        $datos['id_tienda'] = auth()->user()->id_tienda;

        $this->productosService->guardar($datos);

        return redirect()->route('productos.index')->with('success', 'Producto creado en tu tienda.');
    }

    public function edit(int $id)
    {
        $productos = $this->productosService->buscarPorId($id);
        $categorias = $this->categoriasService->listarTodo();
        $tiendas = $this->tiendasService->listarTodo();
        return view('productos.editar', compact('productos', 'categorias', 'tiendas'));
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
