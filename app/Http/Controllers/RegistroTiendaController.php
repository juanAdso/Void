<?php

namespace App\Http\Controllers;

use App\Models\Tienda;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegistroTiendaController extends Controller
{
    public function create()
    {
        return view('auth.registro-tienda');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_tienda' => ['required', 'string', 'max:100'],
            'descripcion_tienda' => ['nullable', 'string'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'correo_tienda' => ['nullable', 'email', 'max:100'],
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'documento' => ['required', 'string', 'max:20', 'unique:usuarios,documento'],
            'correo' => ['required', 'email', 'max:150', 'unique:usuarios,correo'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'documento.unique' => 'El documento ya se encuentra registrado.',
            'correo.unique' => 'El correo electrónico ya se encuentra registrado.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $usuario = DB::transaction(function () use ($validated) {
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
                'password' => Hash::make($validated['password']),
                'rol' => 'administrador',
                'id_tienda' => $tienda->id,
                'estado' => true,
            ]);

            $usuario->assignRole('administrador');

            return $usuario;
        });

        Auth::login($usuario);

        return redirect()->route('dashboard.index')->with('success', '¡Tienda registrada exitosamente!');
    }
}