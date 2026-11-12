<?php

namespace App\Http\Controllers;

use App\Models\Facturas;
use App\Models\Productos;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var User|null $usuario */
        $usuario = auth()->user();

        if (! $usuario) {
            return redirect()->route('login');
        }

        if ($usuario->esSuperadministrador()) {
            $tiendas = Tienda::query()
                ->withCount(['productos', 'usuarios'])
                ->orderBy('nombre')
                ->get();

            $totalTiendas = Tienda::count();
            $tiendasActivas = Tienda::where('estado', true)->count();
            $totalUsuarios = User::count();
            $totalProductos = Productos::count();
            $ventasPagadas = Facturas::where('estado', 'pagada')->count();
            $ingresosGlobales = (float) Facturas::where('estado', 'pagada')->sum('total');

            return view('dashboard.superadministrador', compact(
                'tiendas',
                'totalTiendas',
                'tiendasActivas',
                'totalUsuarios',
                'totalProductos',
                'ventasPagadas',
                'ingresosGlobales'
            ));
        }

        $tiendaId = $usuario->requiereTiendaId();

        if ($usuario->hasRole('empleado')) {
            $ventasHoy = Facturas::where('id_usuario', $usuario->id)
                ->whereDate('fecha', today())
                ->where('estado', 'pagada')
                ->count();

            $ingresosHoy = (float) Facturas::where('id_usuario', $usuario->id)
                ->whereDate('fecha', today())
                ->where('estado', 'pagada')
                ->sum('total');

            $productosTienda = Productos::where('id_tienda', $tiendaId)
                ->where('estado', true)
                ->count();

            $facturasRecientes = Facturas::with('cliente')
                ->where('id_usuario', $usuario->id)
                ->latest('fecha')
                ->limit(5)
                ->get();

            return view('dashboard.empleado', compact(
                'ventasHoy',
                'ingresosHoy',
                'productosTienda',
                'facturasRecientes'
            ));
        }

        $usuariosTienda = User::where('id_tienda', $tiendaId)->pluck('id');
        $totalProductos = Productos::where('id_tienda', $tiendaId)->count();
        $totalEmpleados = User::where('id_tienda', $tiendaId)->where('rol', 'empleado')->count();

        $inicioMes = now()->startOfMonth()->toDateString();
        $finMes = now()->endOfMonth()->toDateString();

        $ventasMes = Facturas::whereIn('id_usuario', $usuariosTienda)
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->where('estado', 'pagada')
            ->count();

        $ingresosMes = (float) Facturas::whereIn('id_usuario', $usuariosTienda)
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->where('estado', 'pagada')
            ->sum('total');

        return view('dashboard.admin', compact(
            'totalProductos',
            'totalEmpleados',
            'ventasMes',
            'ingresosMes'
        ));
    }
}
