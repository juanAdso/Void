<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Productos;
use App\Models\Scopes\TiendaScope;
use App\Models\Tienda;
use Illuminate\Http\Request;

class PublicPortalController extends Controller
{
    public function index()
    {
        $tiendas = Tienda::query()
            ->where('estado', true)
            ->withCount(['productos as total_productos' => function ($query) {
                $query->withoutGlobalScope(TiendaScope::class)->where('estado', true);
            }])
            ->orderBy('nombre')
            ->get();

        return view('welcome', compact('tiendas'));
    }

    public function tienda(int $id)
    {
        $tienda = Tienda::query()->whereKey($id)->where('estado', true)->firstOrFail();

        $productosDestacados = Productos::query()
            ->withoutGlobalScope(TiendaScope::class)
            ->with(['categoria' => fn ($query) => $query->withoutGlobalScope(TiendaScope::class)])
            ->where('id_tienda', $tienda->id)
            ->where('estado', true)
            ->where('stock', '>', 0)
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $categorias = Categoria::query()
            ->withoutGlobalScope(TiendaScope::class)
            ->where('id_tienda', $tienda->id)
            ->where('estado', true)
            ->whereIn('id', function ($query) use ($tienda) {
                $query->select('id_categoria')
                    ->from('productos')
                    ->where('id_tienda', $tienda->id)
                    ->where('estado', true);
            })
            ->orderBy('nombre')
            ->get();

        return view('portal.tienda', compact('tienda', 'productosDestacados', 'categorias'));
    }

    public function catalogo(Request $request)
    {
        $tiendas = Tienda::query()->where('estado', true)->orderBy('nombre')->get();
        $tiendaSeleccionada = null;

        if ($request->has('tienda')) {
            $tiendaId = $request->input('tienda');

            if (empty($tiendaId) || ! ctype_digit((string) $tiendaId)) {
                abort(404, 'Tienda no especificada.');
            }

            $tiendaSeleccionada = Tienda::query()
                ->whereKey((int) $tiendaId)
                ->where('estado', true)
                ->firstOrFail();
        }

        if (! $tiendaSeleccionada) {
            return view('layouts.catalogo', compact('tiendas', 'tiendaSeleccionada'));
        }

        $productosQuery = Productos::query()
            ->withoutGlobalScope(TiendaScope::class)
            ->with([
                'categoria' => fn ($query) => $query->withoutGlobalScope(TiendaScope::class),
                'tienda',
            ])
            ->where('productos.id_tienda', $tiendaSeleccionada->id)
            ->where('productos.estado', true);

        if ($request->input('disponibilidad') === 'agotados') {
            $productosQuery->where('productos.stock', '<=', 0);
        } elseif ($request->input('disponibilidad') !== 'todos') {
            $productosQuery->where('productos.stock', '>', 0);
        }

        if ($request->filled('categoria')) {
            $productosQuery->where('productos.id_categoria', $request->integer('categoria'));
        }

        if ($request->filled('buscar')) {
            $buscar = trim((string) $request->input('buscar'));
            $productosQuery->where(function ($query) use ($buscar) {
                $query->where('productos.nombre', 'like', '%'.$buscar.'%')
                    ->orWhere('productos.descripcion', 'like', '%'.$buscar.'%');
            });
        }

        if ($request->filled('precio_min')) {
            $productosQuery->where('productos.precio', '>=', (float) $request->input('precio_min'));
        }

        if ($request->filled('precio_max')) {
            $productosQuery->where('productos.precio', '<=', (float) $request->input('precio_max'));
        }

        $productos = $productosQuery->orderBy('productos.nombre')->paginate(12)->withQueryString();

        $categorias = Categoria::query()
            ->withoutGlobalScope(TiendaScope::class)
            ->where('id_tienda', $tiendaSeleccionada->id)
            ->where('estado', true)
            ->whereIn('id', function ($query) use ($tiendaSeleccionada) {
                $query->select('id_categoria')
                    ->from('productos')
                    ->where('id_tienda', $tiendaSeleccionada->id)
                    ->where('estado', true);
            })
            ->orderBy('nombre')
            ->get();

        return view('layouts.catalogo', compact('tiendas', 'tiendaSeleccionada', 'categorias', 'productos'));
    }

    public function producto(int $id)
    {
        $producto = Productos::query()
            ->withoutGlobalScope(TiendaScope::class)
            ->with([
                'categoria' => fn ($query) => $query->withoutGlobalScope(TiendaScope::class),
                'tienda',
            ])
            ->whereKey($id)
            ->where('estado', true)
            ->whereHas('tienda', fn ($query) => $query->where('estado', true))
            ->firstOrFail();

        $productosRelacionados = Productos::query()
            ->withoutGlobalScope(TiendaScope::class)
            ->where('id_tienda', $producto->id_tienda)
            ->where('id_categoria', $producto->id_categoria)
            ->where('id', '<>', $producto->id)
            ->where('estado', true)
            ->where('stock', '>', 0)
            ->limit(4)
            ->get();

        return view('portal.producto', compact('producto', 'productosRelacionados'));
    }
}
