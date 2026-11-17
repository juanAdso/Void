<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'correo' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        $user = User::where('correo', $credentials['correo'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            if (! $user->estado) {
                throw ValidationException::withMessages([
                    'correo' => 'Tu cuenta se encuentra inactiva. Comunícate con el administrador.',
                ]);
            }

            if ($user->rol === 'superadministrador') {
                if (! $user->hasRole('superadministrador') || $user->id_tienda !== null) {
                    throw ValidationException::withMessages([
                        'correo' => 'La cuenta global no está configurada correctamente. Comunícate con soporte.',
                    ]);
                }
            } elseif (! $user->id_tienda || ! $user->tienda || ! $user->tienda->estado) {
                throw ValidationException::withMessages([
                    'correo' => 'La empresa asociada a esta cuenta está inactiva o no disponible.',
                ]);
            }
        }

        if (! Auth::attempt(['correo' => $credentials['correo'], 'password' => $credentials['password'], 'estado' => true], $remember)) {
            throw ValidationException::withMessages([
                'correo' => 'El correo o la contraseña son incorrectos.',
            ]);
        }

        $request->session()->regenerate();

        if ($user->hasRole('superadministrador')) {
            return redirect()->route('superadmin.dashboard');
        }

        return redirect()->intended(route('dashboard.index'));
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('inicio')->with('success', 'Sesión cerrada correctamente.');
    }
}
