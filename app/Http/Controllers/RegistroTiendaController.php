<?php

namespace App\Http\Controllers;

use App\Models\Tienda;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegistroTiendaController extends Controller
{
    public function create()
    {
        return view('auth.registro-tienda');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_tienda' => 'required|string|max:150',
            'descripcion_tienda' => 'nullable|string',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string',
            'correo_tienda' => 'nullable|email',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'documento' => 'required|string|unique:usuarios,documento',
            'correo' => 'required|email|unique:usuarios,correo',
            'password' => 'required|string|min:8|confirmed',
        ]);

        DB::transaction(function () use ($validated) {
            $tienda = Tienda::create([
                'nombre' => $validated['nombre_tienda'],
                'descripcion' => $validated['descripcion_tienda'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'correo' => $validated['correo_tienda'] ?? null,
                'estado' => true,
            ]);

            $usuario = User::create([
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'documento' => $validated['documento'],
                'correo' => $validated['correo'],
                'password' => bcrypt($validated['password']),
                'rol' => 'administrador',
                'id_tienda' => $tienda->id,
                'estado' => true,
            ]);

            $usuario->assignRole('administrador');

            Auth::login($usuario);
        });

        return redirect()->route('dashboard.index')->with('success', '¡Tienda registrada exitosamente!');
    }
}