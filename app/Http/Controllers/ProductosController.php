<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductosRequest;
use App\Http\Requests\UpdateProductosRequest;
use App\Models\Productos;
use App\Models\User;
use App\Services\CategoriaService;
use App\Services\ProductosService;
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
        /** @var User $usuario */
        $usuario = auth()->user();
        $tiendaId = $usuario ? $usuario->id_tienda : null;

        // Solo lista los productos que pertenecen a la tienda del usuario
        $productos = $this->productosService->listarTodo($tiendaId);

        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $categorias = $this->categoriasService->listarTodo();

        return view('productos.crear', compact('categorias'));
    }

    public function store(StoreProductosRequest $request)
    {
        $tiendaId = auth()->user()->id_tienda;
        if (! $tiendaId) {
            abort(403, 'No tienes una tienda asignada para crear productos.');
        }

        $datos = $request->validated();
        $datos['id_tienda'] = $tiendaId;

        $this->productosService->guardar($datos);

        return redirect()->route('productos.index')->with('success', 'Producto creado en tu tienda.');
    }

    public function edit(int $id)
    {
        $tiendaId = auth()->user()->id_tienda;
        $productos = $this->productosService->buscarPorId($id, $tiendaId);
        $categorias = $this->categoriasService->listarTodo();

        return view('productos.editar', compact('productos', 'categorias'));
    }

    public function update(int $id, UpdateProductosRequest $request)
    {
        $tiendaId = auth()->user()->id_tienda;
        // Validar que el producto pertenezca a la tienda del usuario
        $this->productosService->buscarPorId($id, $tiendaId);

        $datos = $request->validated();
        unset($datos['id_tienda']); // Prevenir alteración de id_tienda

        $this->productosService->actualizar($id, $datos);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente');
    }

    public function destroy(int $id)
    {
        $tiendaId = auth()->user()->id_tienda;
        $eliminado = $this->productosService->eliminar($id, $tiendaId);

        if (! $eliminado) {
            return redirect()->route('productos.index')->with(
                'error',
                'No se puede eliminar este producto porque está asociado a una factura.'
            );
        }

        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente');
    }
}
