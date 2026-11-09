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

        if ($user && Hash::check($credentials['password'], $user->password) && ! $user->estado) {
            throw ValidationException::withMessages([
                'correo' => 'Tu cuenta se encuentra inactiva. Comunícate con el administrador.',
            ]);
        }

        if (! Auth::attempt(['correo' => $credentials['correo'], 'password' => $credentials['password'], 'estado' => true], $remember)) {
            throw ValidationException::withMessages([
                'correo' => 'El correo o la contraseña son incorrectos.',
            ]);
        }

        $request->session()->regenerate();

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
