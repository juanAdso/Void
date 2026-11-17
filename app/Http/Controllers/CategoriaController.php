<?php
namespace App\Http\Controllers;
use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Models\Categoria;
use App\Services\CategoriaService;
use App\Services\RelacionesBloqueoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CategoriaController extends Controller
{
    public function __construct(private CategoriaService $categoriaService, private RelacionesBloqueoService $relacionesBloqueoService) {}
    public function index() {
        Gate::authorize('viewAny', Categoria::class);
        $tiendaId=(int)(Auth::user()?->id_tienda ?? 0);
        $categorias=$this->categoriaService->listarTodo($tiendaId);
        return view('categoria.index',compact('categorias'));
    }
    public function create() {
        Gate::authorize('create',Categoria::class);
        return view('categoria.crear');
    }
    public function store(StoreCategoriaRequest $request) {
        Gate::authorize('create',Categoria::class);
        $this->categoriaService->guardar($request->validated(),(int)(Auth::user()?->id_tienda ?? 0));
        return redirect()->route('categoria.index')->with('success','Categoría creada correctamente.');
    }
    public function edit(int $id) {
        $categoria=$this->categoriaService->buscarPorId($id,(int)(Auth::user()?->id_tienda ?? 0));
        Gate::authorize('update',$categoria);
        return view('categoria.editar',compact('categoria'));
    }
    public function update(int $categorium,UpdateCategoriaRequest $request) {
        $tiendaId=(int)(Auth::user()?->id_tienda ?? 0);
        $categoria=$this->categoriaService->buscarPorId($categorium,$tiendaId);
        Gate::authorize('update',$categoria);
        $this->categoriaService->actualizar($categorium,$request->validated(),$tiendaId);
        return redirect()->route('categoria.index')->with('success','Categoría actualizada correctamente.');
    }
    public function destroy(int $id) {
        $tiendaId=(int)(Auth::user()?->id_tienda ?? 0);
        $categoria=$this->categoriaService->buscarPorId($id,$tiendaId);
        Gate::authorize('delete',$categoria);
        $bloqueos=$this->relacionesBloqueoService->paraCategoria($id,$tiendaId);
        if ($bloqueos!==[]) {
            $this->relacionesBloqueoService->flash('No se puede eliminar esta categoría','La categoría tiene productos asociados. Puedes abrirlos desde aquí para cambiar su categoría o revisar su estado.',$bloqueos);
            return redirect()->route('categoria.index')->with('error','La categoría conserva productos relacionados. Revisa los registros indicados.');
        }
        if (!$this->categoriaService->eliminar($id,$tiendaId)) {
            return redirect()->route('categoria.index')->with('error','No se pudo eliminar la categoría porque todavía tiene registros asociados.');
        }
        return redirect()->route('categoria.index')->with('success','Categoría eliminada correctamente.');
    }
}
