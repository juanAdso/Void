<?php

namespace App\Http\Controllers;

use App\Models\Facturas;
use App\Models\Productos;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $usuario */
        $usuario = auth()->user();

        // Si no hay usuario autenticado por seguridad redirige al login
        if (!$usuario) {
            return redirect()->route('login');
        }

        $tiendaId = $usuario->id_tienda;

        // ==========================================
        // CASO 1: SI EL USUARIO ES UN EMPLEADO
        // ==========================================
        if ($usuario->hasRole('empleado')) {
            // Ventas realizadas hoy por este empleado
            $ventasHoy = Facturas::where('id_usuario', $usuario->id)
                ->whereDate('fecha', today())
                ->where('estado', 'pagada')
                ->count();

            // Total de dinero cobrado hoy por este empleado
            $ingresosHoy = (float) Facturas::where('id_usuario', $usuario->id)
                ->whereDate('fecha', today())
                ->where('estado', 'pagada')
                ->sum('total');

            // Cantidad de productos disponibles en su tienda
            $productosTienda = Productos::where('id_tienda', $tiendaId)
                ->where('estado', true)
                ->count();

            // Últimas 5 facturas generadas por el empleado
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

        // ==========================================
        // CASO 2: SI EL USUARIO ES EL ADMINISTRADOR
        // ==========================================
        // Obtiene todos los usuarios (dueño y empleados) de su tienda
        $usuariosTienda = User::where('id_tienda', $tiendaId)->pluck('id');

        // Total de productos en inventario de su tienda
        $totalProductos = Productos::where('id_tienda', $tiendaId)->count();

        // Cantidad de empleados de su tienda
        $totalEmpleados = User::where('id_tienda', $tiendaId)
            ->where('rol', 'empleado')
            ->count();

        $inicioMes = now()->startOfMonth()->toDateString();
        $finMes = now()->endOfMonth()->toDateString();

        // Ventas del mes en su tienda (excluyendo facturas anuladas)
        $ventasMes = Facturas::whereIn('id_usuario', $usuariosTienda)
            ->whereBetween('fecha', [$inicioMes, $finMes])
            ->where('estado', 'pagada')
            ->count();

        // Dinero recaudado en el mes
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
