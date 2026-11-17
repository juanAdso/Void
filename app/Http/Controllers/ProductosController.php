<?php
namespace App\Http\Controllers;
use App\Http\Requests\StoreProductosRequest;
use App\Http\Requests\UpdateProductosRequest;
use App\Models\Productos;
use App\Services\CategoriaService;
use App\Services\ProductosService;
use App\Services\RelacionesBloqueoService;

class ProductosController extends Controller
{
    public function __construct(private ProductosService $productosService,private CategoriaService $categoriasService,private RelacionesBloqueoService $relacionesBloqueoService) {}
    public function index() {
        $this->authorize('viewAny',Productos::class);
        $tiendaId=(int)auth()->user()->id_tienda;
        $productos=$this->productosService->listarTodo($tiendaId);
        return view('productos.index',compact('productos'));
    }
    public function create() {
        $this->authorize('create',Productos::class);
        $tiendaId=(int)auth()->user()->id_tienda;
        $categorias=$this->categoriasService->listarTodo($tiendaId);
        return view('productos.crear',compact('categorias'));
    }
    public function store(StoreProductosRequest $request) {
        $this->authorize('create',Productos::class);
        $tiendaId=(int)auth()->user()->id_tienda;
        $this->productosService->guardar($request->validated(),$tiendaId);
        return redirect()->route('productos.index')->with('success','Producto creado en tu tienda.');
    }
    public function edit(int $id) {
        $tiendaId=(int)auth()->user()->id_tienda;
        $producto=$this->productosService->buscarPorId($id,$tiendaId);
        $this->authorize('update',$producto);
        $categorias=$this->categoriasService->listarTodo($tiendaId);
        return view('productos.editar',['productos'=>$producto,'categorias'=>$categorias]);
    }
    public function update(int $id,UpdateProductosRequest $request) {
        $tiendaId=(int)auth()->user()->id_tienda;
        $producto=$this->productosService->buscarPorId($id,$tiendaId);
        $this->authorize('update',$producto);
        $datos=$request->validated();
        unset($datos['id_tienda']);
        $this->productosService->actualizar($id,$datos,$tiendaId);
        return redirect()->route('productos.index')->with('success','Producto actualizado correctamente.');
    }
    public function destroy(int $id) {
        $tiendaId=(int)auth()->user()->id_tienda;
        $producto=$this->productosService->buscarPorId($id,$tiendaId);
        $this->authorize('delete',$producto);
        $bloqueos=$this->relacionesBloqueoService->paraProducto($id,$tiendaId);
        if ($bloqueos!==[]) {
            $this->relacionesBloqueoService->flash('No se puede eliminar este producto','El producto aparece en facturas o en el historial de inventario. Estos registros deben conservarse para proteger la trazabilidad del negocio.',$bloqueos);
            return redirect()->route('productos.index')->with('error','El producto tiene relaciones o movimientos de inventario que impiden eliminarlo.');
        }
        if (!$this->productosService->eliminar($id,$tiendaId)) {
            return redirect()->route('productos.index')->with('error','No se pudo eliminar el producto porque todavía tiene registros asociados.');
        }
        return redirect()->route('productos.index')->with('success','Producto eliminado correctamente.');
    }
}
