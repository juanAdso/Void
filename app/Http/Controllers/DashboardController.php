<?php

namespace App\Http\Controllers;

use App\Models\Facturas;
use App\Models\Productos;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        $usuario = Auth::user();

        if (! $usuario) {
            return redirect()->route('login');
        }

        $tiendaId = $usuario->id_tienda;

        if ($usuario->hasRole('empleado')) {

            $ventasHoy = Facturas::where('id_usuario', $usuario->id)
                ->whereDate('fecha', today())
                ->whereIn('estado', ['pendiente', 'pagada'])
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
                ->orderByDesc('id')
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

        $totalEmpleados = User::where('id_tienda', $tiendaId)
            ->where('rol', 'empleado')
            ->count();

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
