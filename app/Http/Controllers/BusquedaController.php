<?php

namespace App\Http\Controllers;

use App\Services\BusquedaService;
use Illuminate\Http\Request;

class BusquedaController extends Controller
{
    public function index(Request $request, BusquedaService $busquedaService)
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $termino = trim((string) $request->query('q', ''));

        $secciones = $termino !== '' && mb_strlen($termino) >= 2
            ? $busquedaService->buscar($request->user(), $termino)
            : [];

        $totalResultados = collect($secciones)
            ->sum(fn (array $seccion) => count($seccion['items']));

        return view('busqueda.index', compact(
            'termino',
            'secciones',
            'totalResultados'
        ));
    }
}
