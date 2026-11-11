<?php

namespace App\Http\Controllers;

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
     * Genera y envía un código de 6 dígitos al correo correspondiente.
     */
    public function sendResetCode(Request $request)
    {
        $throttleKey = 'password-reset:'.$request->ip();

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

        $inputCorreo = trim($request->input('correo'));

        // 1. Buscar si corresponde directamente a un usuario
        $user = User::where('correo', $inputCorreo)->first();
        $tienda = null;

        // 2. Si no es un usuario directo, buscar si corresponde a una tienda
        if (! $user) {
            $tienda = Tienda::where('correo', $inputCorreo)->first();
            if ($tienda) {
                // Localizar exclusivamente al administrador asociado a esa tienda
                $user = User::where('id_tienda', $tienda->id)
                    ->where('rol', 'administrador')
                    ->first();
            }
        }

        // Si no encontramos usuario o si la cuenta está inactiva
        if (! $user) {
            return back()->withErrors([
                'correo' => 'No encontramos ninguna cuenta o tienda asociada a este correo electrónico.',
            ])->withInput();
        }

        if (! $user->estado) {
            return back()->withErrors([
                'correo' => 'El usuario asociado se encuentra inactivo. Comunícate con el administrador.',
            ])->withInput();
        }

        // Generar código numérico seguro de 6 dígitos
        $codigo = (string) random_int(100000, 999999);
        $correoDestino = $inputCorreo;

        // Almacenar en caché durante 15 minutos vinculado al usuario y correo
        Cache::put('password_reset_code_'.$codigo, [
            'user_id' => $user->id,
            'correo' => $correoDestino,
        ], now()->addMinutes(15));

        // Enviar correo electrónico con el código
        $nombreDestinatario = $tienda ? $tienda->nombre : $user->nombre;
        $mensajeCorreo = "Hola {$nombreDestinatario},\n\n"
            ."Has solicitado restablecer tu contraseña en el sistema VOID.\n"
            ."Tu código de verificación de 6 dígitos es: {$codigo}\n\n"
            ."Este código es válido por 15 minutos.\n"
            .'Si no solicitaste este cambio, puedes ignorar este mensaje.';

        try {
            Mail::raw($mensajeCorreo, function ($message) use ($correoDestino, $nombreDestinatario) {
                $message->to($correoDestino, $nombreDestinatario)
                    ->subject('Código de recuperación de contraseña - VOID');
            });
        } catch (\Throwable $e) {
            Cache::forget('password_reset_code_'.$codigo);
            report($e);

            return back()->withErrors([
                'correo' => 'No se pudo enviar el correo de recuperación. Por favor, intenta de nuevo más tarde.',
            ])->withInput();
        }

        return redirect()->route('password.code')
            ->with('status', 'Hemos enviado un código de verificación de 6 dígitos al correo: '.$correoDestino)
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
     * Valida el código y actualiza la contraseña del usuario.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'codigo' => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'codigo.required' => 'El código de verificación es obligatorio.',
            'codigo.size' => 'El código debe tener exactamente 6 dígitos.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $codigo = trim($request->input('codigo'));
        $cacheData = Cache::get('password_reset_code_'.$codigo);

        if (! $cacheData) {
            return back()->withErrors([
                'codigo' => 'El código de verificación es inválido o ha expirado.',
            ])->withInput();
        }

        $user = User::find($cacheData['user_id']);

        if (! $user) {
            return back()->withErrors([
                'codigo' => 'No se encontró el usuario para este código de recuperación.',
            ]);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        // Eliminar código de la caché tras ser usado
        Cache::forget('password_reset_code_'.$codigo);

        return redirect()->route('login')
            ->with('status', 'Tu contraseña ha sido restablecida con éxito. Ya puedes iniciar sesión.');
    }
}
