<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Facturas;
use App\Models\Productos;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $inicioMes = now()->startOfMonth()->toDateString();
        $finMes = now()->endOfMonth()->toDateString();

        $totalUsers = User::count();
        $totalProducts = Productos::count();
        $totalSales = Facturas::whereBetween('fecha', [$inicioMes, $finMes])
            ->where('estado', 'pagada')
            ->count();
        $totalRevenue = (float) Facturas::whereBetween('fecha', [$inicioMes, $finMes])
            ->where('estado', 'pagada')
            ->sum('total');

        $meses = collect(range(5, 0))->map(function (int $mesesAtras) {
            return now()->startOfMonth()->subMonths($mesesAtras);
        });

        $chartData = [
            'months' => $meses->map(fn (Carbon $mes) => $mes->translatedFormat('M'))->values()->all(),
            'sales' => $meses->map(function (Carbon $mes) {
                return Facturas::where('estado', 'pagada')
                    ->whereBetween('fecha', [$mes->copy()->startOfMonth()->toDateString(), $mes->copy()->endOfMonth()->toDateString()])
                    ->count();
            })->values()->all(),
            'users' => $meses->map(function (Carbon $mes) {
                return User::whereBetween('created_at', [$mes->copy()->startOfMonth(), $mes->copy()->endOfMonth()])->count();
            })->values()->all(),
            'categoryLabels' => Categoria::query()
                ->withCount('productos')
                ->orderByDesc('productos_count')
                ->limit(5)
                ->pluck('nombre_categoria')
                ->values()
                ->all(),
            'categoryValues' => Categoria::query()
                ->withCount('productos')
                ->orderByDesc('productos_count')
                ->limit(5)
                ->pluck('productos_count')
                ->map(fn ($cantidad) => (int) $cantidad)
                ->values()
                ->all(),
        ];

        $recentActivity = Facturas::with('cliente')
            ->latest('fecha')
            ->latest('id')
            ->limit(6)
            ->get()
            ->map(function (Facturas $factura) {
                $nombre = trim(($factura->cliente?->nombre ?? '') . ' ' . ($factura->cliente?->apellido ?? ''));

                return [
                    'id' => $factura->id,
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($nombre ?: 'Cliente'),
                    'name' => $nombre ?: 'Cliente eliminado',
                    'description' => 'Factura #' . $factura->id . ' por $' . number_format((float) $factura->total, 2, ',', '.'),
                    'status' => ucfirst((string) $factura->estado),
                    'date' => $factura->fecha?->format('d/m/Y') ?? 'Sin fecha',
                ];
            })
            ->all();

        return view('dashboard.index', compact(
            'totalUsers',
            'totalProducts',
            'totalSales',
            'totalRevenue',
            'recentActivity',
            'chartData'
        ));
    }
}
