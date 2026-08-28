<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard principal.
     *
     * Reemplaza los valores de ejemplo por consultas reales a tus modelos,
     * por ejemplo: User::count(), Product::count(), Order::sum('total'), etc.
     */
    public function index(Request $request)
    {
        $totalUsers    = 8241;
        $totalProducts = 1523;
        $totalSales    = 962;
        $totalRevenue  = 48920.50;

        $recentActivity = [
            [
                'avatar'      => 'https://ui-avatars.com/api/?name=Laura+Gomez',
                'name'        => 'Laura Gómez',
                'description' => 'Registró un nuevo producto en la categoría Electrónica',
                'status'      => 'Activo',
                'date'        => now()->subHours(2)->format('d/m/Y H:i'),
            ],
            [
                'avatar'      => 'https://ui-avatars.com/api/?name=Carlos+Ruiz',
                'name'        => 'Carlos Ruiz',
                'description' => 'Solicitud de reembolso pendiente de revisión',
                'status'      => 'Pendiente',
                'date'        => now()->subHours(5)->format('d/m/Y H:i'),
            ],
            [
                'avatar'      => 'https://ui-avatars.com/api/?name=Maria+Torres',
                'name'        => 'María Torres',
                'description' => 'Cuenta suspendida por incumplimiento de políticas',
                'status'      => 'Inactivo',
                'date'        => now()->subDay()->format('d/m/Y H:i'),
            ],
            [
                'avatar'      => 'https://ui-avatars.com/api/?name=Jorge+Diaz',
                'name'        => 'Jorge Díaz',
                'description' => 'Actualizó la información de su perfil',
                'status'      => 'Activo',
                'date'        => now()->subDays(2)->format('d/m/Y H:i'),
            ],
        ];

        $chartData = [
            'months'         => ['Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago'],
            'sales'          => [320, 410, 380, 512, 460, 590],
            'users'          => [120, 190, 150, 260, 210, 300],
            'categoryLabels' => ['Electrónica', 'Ropa', 'Hogar', 'Deportes', 'Otros'],
            'categoryValues' => [38, 24, 18, 12, 8],
        ];

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
