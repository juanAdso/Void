<?php

namespace Tests\Feature;

use App\Mail\CodigoRecuperacionMail;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class RecuperarContrasenaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_se_puede_ver_la_pantalla_de_solicitud_de_recuperacion(): void
    {
        $response = $this->get(route('password.request'));
        $response->assertStatus(200);
        $response->assertSee('Recuperar contraseña');
    }

    public function test_solicitar_codigo_con_correo_de_usuario_existente(): void
    {
        $user = User::factory()->create([
            'correo' => 'admin@void.test',
            'rol' => 'administrador',
            'estado' => true,
        ]);

        $response = $this->post(route('password.email'), [
            'correo' => 'admin@void.test',
        ]);

        $response->assertRedirect(route('password.code'));
        $response->assertSessionHas('status');

        Mail::assertSent(CodigoRecuperacionMail::class, function (CodigoRecuperacionMail $mail) use ($user) {
            $data = Cache::get('password_reset_code_'.$mail->codigo);

            return $mail->hasTo('admin@void.test')
                && $mail->nombreDestinatario === $user->nombre
                && $data
                && (int) $data['user_id'] === (int) $user->id
                && $data['correo'] === 'admin@void.test';
        });
    }

    public function test_empleado_puede_solicitar_codigo_con_su_correo_personal(): void
    {
        $tienda = Tienda::create([
            'nombre' => 'Tienda del Empleado',
            'correo' => 'contacto@tiendaempleado.test',
            'estado' => true,
        ]);

        $empleado = User::factory()->create([
            'correo' => 'empleado@void.test',
            'rol' => 'empleado',
            'id_tienda' => $tienda->id,
            'estado' => true,
        ]);

        $response = $this->post(route('password.email'), [
            'correo' => 'empleado@void.test',
        ]);

        $response->assertRedirect(route('password.code'));
        $response->assertSessionHas('status');

        Mail::assertSent(CodigoRecuperacionMail::class, function (CodigoRecuperacionMail $mail) use ($empleado) {
            $data = Cache::get('password_reset_code_'.$mail->codigo);

            return $mail->hasTo('empleado@void.test')
                && $mail->nombreDestinatario === $empleado->nombre
                && $data
                && (int) $data['user_id'] === (int) $empleado->id
                && $data['correo'] === 'empleado@void.test';
        });
    }

    public function test_superadministrador_puede_solicitar_codigo_con_su_correo_personal(): void
    {
        $superadministrador = User::factory()->create([
            'correo' => 'superadmin@void.test',
            'rol' => 'superadministrador',
            'id_tienda' => null,
            'estado' => true,
        ]);

        $response = $this->post(route('password.email'), [
            'correo' => 'superadmin@void.test',
        ]);

        $response->assertRedirect(route('password.code'));
        $response->assertSessionHas('status');

        Mail::assertSent(CodigoRecuperacionMail::class, function (CodigoRecuperacionMail $mail) use ($superadministrador) {
            $data = Cache::get('password_reset_code_'.$mail->codigo);

            return $mail->hasTo('superadmin@void.test')
                && $mail->nombreDestinatario === $superadministrador->nombre
                && $data
                && (int) $data['user_id'] === (int) $superadministrador->id
                && $data['correo'] === 'superadmin@void.test';
        });
    }

    public function test_solicitar_codigo_con_correo_de_tienda(): void
    {
        $tienda = Tienda::create([
            'nombre' => 'Tienda Central',
            'correo' => 'contacto@tiendacentral.com',
            'estado' => true,
        ]);

        $admin = User::factory()->create([
            'correo' => 'admin_tienda@void.test',
            'rol' => 'administrador',
            'id_tienda' => $tienda->id,
            'estado' => true,
        ]);

        $response = $this->post(route('password.email'), [
            'correo' => 'contacto@tiendacentral.com',
        ]);

        $response->assertRedirect(route('password.code'));
        $response->assertSessionHas('status');

        Mail::assertSent(CodigoRecuperacionMail::class, function (CodigoRecuperacionMail $mail) use ($admin) {
            $data = Cache::get('password_reset_code_'.$mail->codigo);

            return $mail->hasTo('contacto@tiendacentral.com')
                && $mail->nombreDestinatario === 'Tienda Central'
                && $data
                && (int) $data['user_id'] === (int) $admin->id
                && $data['correo'] === 'contacto@tiendacentral.com';
        });
    }

    public function test_intento_de_recuperar_contrasena_de_otra_tienda_no_afecta_administradores_ajenos(): void
    {
        $tienda1 = Tienda::create([
            'nombre' => 'Tienda Uno',
            'correo' => 'contacto@tiendauno.com',
            'estado' => true,
        ]);

        $admin1 = User::factory()->create([
            'correo' => 'admin1@tiendauno.com',
            'rol' => 'administrador',
            'id_tienda' => $tienda1->id,
            'estado' => true,
        ]);

        $tienda2 = Tienda::create([
            'nombre' => 'Tienda Dos',
            'correo' => 'contacto@tiendados.com',
            'estado' => true,
        ]);

        $admin2 = User::factory()->create([
            'correo' => 'admin2@tiendados.com',
            'rol' => 'administrador',
            'id_tienda' => $tienda2->id,
            'estado' => true,
        ]);

        $response = $this->post(route('password.email'), [
            'correo' => 'contacto@tiendados.com',
        ]);

        $response->assertRedirect(route('password.code'));

        Mail::assertSent(CodigoRecuperacionMail::class, function (CodigoRecuperacionMail $mail) use ($admin1, $admin2) {
            $data = Cache::get('password_reset_code_'.$mail->codigo);

            return $mail->hasTo('contacto@tiendados.com')
                && $data
                && (int) $data['user_id'] === (int) $admin2->id
                && (int) $data['user_id'] !== (int) $admin1->id;
        });
    }

    public function test_tienda_sin_administrador_asociado_no_devuelve_administrador_global(): void
    {
        User::factory()->create([
            'correo' => 'global@void.test',
            'rol' => 'administrador',
            'id_tienda' => null,
            'estado' => true,
        ]);

        Tienda::create([
            'nombre' => 'Tienda Sola',
            'correo' => 'sola@tienda.com',
            'estado' => true,
        ]);

        $response = $this->post(route('password.email'), [
            'correo' => 'sola@tienda.com',
        ]);

        $response->assertSessionHasErrors(['correo']);
    }

    public function test_usuario_inactivo_no_puede_recuperar_contrasena(): void
    {
        User::factory()->create([
            'correo' => 'inactivo@void.test',
            'estado' => false,
        ]);

        $response = $this->post(route('password.email'), [
            'correo' => 'inactivo@void.test',
        ]);

        $response->assertSessionHasErrors(['correo']);
    }

    public function test_fallo_en_el_servicio_de_correo_no_simula_exito(): void
    {
        $user = User::factory()->create([
            'correo' => 'admin.fallo@void.test',
            'estado' => true,
        ]);

        Mail::shouldReceive('to')
            ->once()
            ->with('admin.fallo@void.test', $user->nombre)
            ->andThrow(new \Exception('SMTP connection timeout'));

        $response = $this->post(route('password.email'), [
            'correo' => 'admin.fallo@void.test',
        ]);

        $response->assertSessionHasErrors(['correo']);
    }

    public function test_correo_de_recuperacion_renderiza_el_codigo_y_la_vista_de_void(): void
    {
        $correo = new CodigoRecuperacionMail('Laura', '123456');
        $contenido = $correo->render();

        $this->assertStringContainsString('123456', $contenido);
        $this->assertStringContainsString('Laura', $contenido);
        $this->assertStringContainsString('VOID', $contenido);
        $this->assertStringContainsString('15 minutos', $contenido);
    }

    public function test_codigo_vencido_es_rechazado(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('antiguaPassword123'),
            'estado' => true,
        ]);

        $codigo = '654321';
        Cache::put('password_reset_code_'.$codigo, [
            'user_id' => $user->id,
            'correo' => $user->correo,
        ], now()->subMinutes(1));

        $response = $this->post(route('password.update'), [
            'codigo' => $codigo,
            'password' => 'nuevaPassword123',
            'password_confirmation' => 'nuevaPassword123',
        ]);

        $response->assertSessionHasErrors(['codigo']);
    }

    public function test_codigo_no_puede_ser_reutilizado(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('antiguaPassword123'),
            'estado' => true,
        ]);

        $codigo = '112233';
        Cache::put('password_reset_code_'.$codigo, [
            'user_id' => $user->id,
            'correo' => $user->correo,
        ], now()->addMinutes(15));

        $response1 = $this->post(route('password.update'), [
            'codigo' => $codigo,
            'password' => 'nuevaPassword123',
            'password_confirmation' => 'nuevaPassword123',
        ]);
        $response1->assertRedirect(route('login'));

        $response2 = $this->post(route('password.update'), [
            'codigo' => $codigo,
            'password' => 'otraPassword456',
            'password_confirmation' => 'otraPassword456',
        ]);
        $response2->assertSessionHasErrors(['codigo']);
    }

    public function test_codigo_invalido_es_rechazado(): void
    {
        $response = $this->post(route('password.update'), [
            'codigo' => '999999',
            'password' => 'nuevaPassword123',
            'password_confirmation' => 'nuevaPassword123',
        ]);

        $response->assertSessionHasErrors(['codigo']);
    }

    public function test_codigo_valido_permite_actualizar_contrasena(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('antiguaPassword123'),
            'estado' => true,
        ]);

        $codigo = '123456';
        Cache::put('password_reset_code_'.$codigo, [
            'user_id' => $user->id,
            'correo' => $user->correo,
        ], now()->addMinutes(15));

        $response = $this->post(route('password.update'), [
            'codigo' => $codigo,
            'password' => 'nuevaPassword123',
            'password_confirmation' => 'nuevaPassword123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        $user->refresh();
        $this->assertTrue(Hash::check('nuevaPassword123', $user->password));
        $this->assertNull(Cache::get('password_reset_code_'.$codigo));
    }

    public function test_usuario_inactivo_no_puede_restablecer_contrasena_con_un_codigo_emitido_previamente(): void
    {
        $user = User::factory()->create([
            'estado' => false,
        ]);

        $codigo = '987654';
        Cache::put('password_reset_code_'.$codigo, [
            'user_id' => $user->id,
            'correo' => $user->correo,
        ], now()->addMinutes(15));

        $response = $this->post(route('password.update'), [
            'codigo' => $codigo,
            'password' => 'nuevaPassword123',
            'password_confirmation' => 'nuevaPassword123',
        ]);

        $response->assertSessionHasErrors(['codigo']);
        $this->assertNull(Cache::get('password_reset_code_'.$codigo));
    }

    public function test_limita_los_intentos_de_verificacion_de_codigos(): void
    {
        $throttleKey = 'password-reset-verify:127.0.0.1';
        RateLimiter::clear($throttleKey);

        for ($intento = 0; $intento < 10; $intento++) {
            $this->post(route('password.update'), [
                'codigo' => str_pad((string) (700000 + $intento), 6, '0', STR_PAD_LEFT),
                'password' => 'nuevaPassword123',
                'password_confirmation' => 'nuevaPassword123',
            ])->assertSessionHasErrors(['codigo']);
        }

        $response = $this->post(route('password.update'), [
            'codigo' => '800000',
            'password' => 'nuevaPassword123',
            'password_confirmation' => 'nuevaPassword123',
        ]);

        $response->assertSessionHasErrors(['codigo']);
        $this->assertStringContainsString('Demasiados intentos', session('errors')->first('codigo'));

        RateLimiter::clear($throttleKey);
    }
}
