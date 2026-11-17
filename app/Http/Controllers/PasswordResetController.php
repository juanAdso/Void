<?php

namespace App\Http\Controllers;

use App\Mail\CodigoRecuperacionMail;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class PasswordResetController extends Controller
{
    /**
     * Muestra la vista para solicitar el código de recuperación.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Genera y envía un código de recuperación a cualquier usuario activo.
     */
    public function sendResetCode(Request $request)
    {
        $throttleKey = 'password-reset:' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'correo' => "Demasiados intentos. Por favor, intenta de nuevo en {$seconds} segundos.",
            ])->withInput();
        }

        $request->validate([
            'correo' => ['required', 'email'],
        ], [
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'Ingresa un correo electrónico válido.',
        ]);

        RateLimiter::hit($throttleKey, 300);

        $correoDestino = trim($request->input('correo'));

        /*
         * Buscar primero por el correo personal del usuario.
         * Esta consulta no filtra por rol: permite recuperar cuentas de
         * administradores, empleados y superadministradores.
         */
        $user = User::where('correo', $correoDestino)->first();
        $tienda = null;

        /*
         * Si el correo no pertenece directamente a un usuario, permitir
         * la recuperación mediante el correo general de una tienda.
         * En ese caso, el código queda vinculado a su administrador activo.
         */
        if (! $user) {
            $tienda = Tienda::where('correo', $correoDestino)->first();

            if ($tienda) {
                $user = User::where('id_tienda', $tienda->id)
                    ->where('rol', 'administrador')
                    ->where('estado', true)
                    ->first();
            }
        }

        if (! $user) {
            return back()->withErrors([
                'correo' => 'No encontramos ninguna cuenta activa asociada a este correo electrónico.',
            ])->withInput();
        }

        if (! $user->estado) {
            return back()->withErrors([
                'correo' => 'El usuario asociado se encuentra inactivo. Comunícate con el administrador.',
            ])->withInput();
        }

        /*
         * Generar un código numérico seguro de seis dígitos.
         * Se comprueba que no exista otro código activo con el mismo valor.
         */
        do {
            $codigo = (string) random_int(100000, 999999);
            $cacheKey = 'password_reset_code_' . $codigo;
        } while (Cache::has($cacheKey));

        $nombreDestinatario = $tienda ? $tienda->nombre : $user->nombre;

        /*
         * Asociar el código al usuario correcto durante 15 minutos.
         */
        Cache::put($cacheKey, [
            'user_id' => $user->id,
            'correo' => $correoDestino,
        ], now()->addMinutes(15));

        try {
            Mail::to($correoDestino, $nombreDestinatario)
                ->send(new CodigoRecuperacionMail($nombreDestinatario, $codigo));
        } catch (\Throwable $e) {
            Cache::forget($cacheKey);
            report($e);

            return back()->withErrors([
                'correo' => 'No se pudo enviar el correo de recuperación. Por favor, intenta de nuevo más tarde.',
            ])->withInput();
        }

        return redirect()->route('password.code')
            ->with('status', 'Hemos enviado un código de verificación de 6 dígitos al correo: ' . $correoDestino)
            ->with('correo_destino', $correoDestino);
    }

    /**
     * Muestra la vista para ingresar el código y la nueva contraseña.
     */
    public function showResetForm()
    {
        return view('auth.verify-code');
    }

    /**
     * Valida el código y actualiza la contraseña de la cuenta vinculada.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'codigo' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'codigo.required' => 'El código de verificación es obligatorio.',
            'codigo.size' => 'El código debe tener exactamente 6 dígitos.',
            'codigo.regex' => 'El código debe contener únicamente seis números.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $codigo = trim($request->input('codigo'));
        $cacheKey = 'password_reset_code_' . $codigo;
        $cacheData = Cache::get($cacheKey);

        if (! $cacheData) {
            return back()->withErrors([
                'codigo' => 'El código de verificación es inválido o ha expirado.',
            ])->withInput();
        }

        $user = User::find($cacheData['user_id']);

        if (! $user) {
            Cache::forget($cacheKey);

            return back()->withErrors([
                'codigo' => 'No se encontró el usuario asociado a este código de recuperación.',
            ]);
        }

        if (! $user->estado) {
            Cache::forget($cacheKey);

            return back()->withErrors([
                'codigo' => 'La cuenta está inactiva. Comunícate con el administrador.',
            ]);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        // Eliminar el código para impedir que vuelva a utilizarse.
        Cache::forget($cacheKey);

        return redirect()->route('login')
            ->with('status', 'Tu contraseña ha sido restablecida con éxito. Ya puedes iniciar sesión.');
    }
}
