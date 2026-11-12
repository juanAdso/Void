<?php

namespace App\Http\Controllers;

use App\Models\Facturas;
use App\Models\MovimientoInventario;
use App\Models\Productos;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    public function index(Request $request)
    {
        $usuario = auth()->user();
        $tiendaId = $usuario?->id_tienda;

        if (!$tiendaId) {
            abort(403, 'No tienes una tienda asignada para consultar reportes.');
        }

        $productosStockBajo = Productos::query()
            ->with('categoria')
            ->where('id_tienda', $tiendaId)
            ->where('stock', '>', 0)
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->get();

        $productosAgotados = Productos::query()
            ->with('categoria')
            ->where('id_tienda', $tiendaId)
            ->where('stock', '<=', 0)
            ->orderBy('nombre')
            ->get();

        $movimientos = MovimientoInventario::query()
            ->with(['producto', 'usuario'])
            ->where('id_tienda', $tiendaId)
            ->latest('fecha')
            ->paginate(15, ['*'], 'movimientos_page')
            ->withQueryString();

        $usuariosTienda = User::where('id_tienda', $tiendaId)->pluck('id');

        $productosMasVendidos = DB::table('detalle_factura')
            ->join('facturas', 'detalle_factura.id_factura', '=', 'facturas.id')
            ->join('productos', 'detalle_factura.id_producto', '=', 'productos.id')
            ->where('productos.id_tienda', $tiendaId)
            ->where('facturas.estado', '!=', 'anulada')
            ->select(
                'productos.id',
                'productos.nombre',
                DB::raw('SUM(detalle_factura.cantidad) as total_vendido'),
                DB::raw('SUM(detalle_factura.subtotal) as total_ingresos')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('total_vendido')
            ->limit(10)
            ->get();

        $ventasHoy = Facturas::query()
            ->whereIn('id_usuario', $usuariosTienda)
            ->whereDate('fecha', today())
            ->where('estado', 'pagada')
            ->count();

        $ingresosHoy = (float) Facturas::query()
            ->whereIn('id_usuario', $usuariosTienda)
            ->whereDate('fecha', today())
            ->where('estado', 'pagada')
            ->sum('total');

        $ventasMes = Facturas::query()
            ->whereIn('id_usuario', $usuariosTienda)
            ->whereBetween('fecha', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->where('estado', 'pagada')
            ->count();

        $ingresosMes = (float) Facturas::query()
            ->whereIn('id_usuario', $usuariosTienda)
            ->whereBetween('fecha', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->where('estado', 'pagada')
            ->sum('total');

        $fechaInicio = $request->input('fecha_inicio', now()->subDays(30)->toDateString());
        $fechaFin = $request->input('fecha_fin', now()->toDateString());

        $ingresosPeriodo = (float) Facturas::query()
            ->whereIn('id_usuario', $usuariosTienda)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', 'pagada')
            ->sum('total');

        $ventasPeriodo = Facturas::query()
            ->whereIn('id_usuario', $usuariosTienda)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', 'pagada')
            ->count();

        $ventasPorEmpleado = User::query()
            ->where('id_tienda', $tiendaId)
            ->where('rol', 'empleado')
            ->withCount(['facturas as total_facturas_pagadas' => function ($q) {
                $q->where('estado', 'pagada');
            }])
            ->withSum(['facturas as total_ingresos_generados' => function ($q) {
                $q->where('estado', 'pagada');
            }], 'total')
            ->get();

        return view('reportes.index', compact(
            'productosStockBajo',
            'productosAgotados',
            'movimientos',
            'productosMasVendidos',
            'ventasHoy',
            'ingresosHoy',
            'ventasMes',
            'ingresosMes',
            'ingresosPeriodo',
            'ventasPeriodo',
            'fechaInicio',
            'fechaFin',
            'ventasPorEmpleado'
        ));
    }
}
