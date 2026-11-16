<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Services\CategoriaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CategoriaController extends Controller
{
    public function __construct(private CategoriaService $categoriaService) {}

    public function index()
    {
        Gate::authorize('viewAny', Categoria::class);
        $tiendaId = (int) (Auth::user()?->id_tienda ?? 0);
        $categorias = $this->categoriaService->listarTodo($tiendaId);

        return view('categoria.index', compact('categorias'));
    }

    public function create()
    {
        Gate::authorize('create', Categoria::class);

        return view('categoria.crear');
    }

    public function store(StoreCategoriaRequest $request)
    {
        Gate::authorize('create', Categoria::class);
        $this->categoriaService->guardar($request->validated(), (int) (Auth::user()?->id_tienda ?? 0));

        return redirect()->route('categoria.index')->with('success', 'Categoría creada correctamente.');
    }

    public function edit(int $id)
    {
        $categoria = $this->categoriaService->buscarPorId($id, (int) (Auth::user()?->id_tienda ?? 0));
        Gate::authorize('update', $categoria);

        return view('categoria.editar', compact('categoria'));
    }

    public function update(int $categorium, UpdateCategoriaRequest $request)
    {
        $idTienda = (int) (Auth::user()?->id_tienda ?? 0);

        $categoria = $this->categoriaService->buscarPorId($categorium, $idTienda);

        Gate::authorize('update', $categoria);

        $this->categoriaService->actualizar(
            $categorium,
            $request->validated(),
            $idTienda
        );

        return redirect()->route('categoria.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(int $id)
    {
        $categoria = $this->categoriaService->buscarPorId($id, (int) (Auth::user()?->id_tienda ?? 0));
        Gate::authorize('delete', $categoria);

        if (! $this->categoriaService->eliminar($id, (int) (Auth::user()?->id_tienda ?? 0))) {
            return redirect()->route('categoria.index')->with('error', 'No se puede eliminar una categoría que tiene productos asociados.');
        }

        return redirect()->route('categoria.index')->with('success', 'Categoría eliminada correctamente.');
    }
}
