<?php

namespace Tests\Feature;

use App\Models\Tienda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
            'estado' => true,
        ]);

        $response = $this->post(route('password.email'), [
            'correo' => 'admin@void.test',
        ]);

        $response->assertRedirect(route('password.code'));
        $response->assertSessionHas('status');

        // Verificar que se haya guardado un código en caché
        $cachedKeys = [];
        for ($i = 100000; $i <= 999999; $i++) {
            if ($data = Cache::get('password_reset_code_'.$i)) {
                $cachedKeys[] = $i;
                $this->assertEquals($user->id, $data['user_id']);
                break;
            }
        }
        $this->assertNotEmpty($cachedKeys, 'No se generó código en la caché');
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

        $found = false;
        for ($i = 100000; $i <= 999999; $i++) {
            if ($data = Cache::get('password_reset_code_'.$i)) {
                $this->assertEquals($admin->id, $data['user_id']);
                $this->assertEquals('contacto@tiendacentral.com', $data['correo']);
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'No se generó el código vinculado al administrador de la tienda');
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

        // Solicitar código para tienda 2
        $response = $this->post(route('password.email'), [
            'correo' => 'contacto@tiendados.com',
        ]);

        $response->assertRedirect(route('password.code'));

        $found = false;
        for ($i = 100000; $i <= 999999; $i++) {
            if ($data = Cache::get('password_reset_code_'.$i)) {
                $this->assertEquals($admin2->id, $data['user_id']);
                $this->assertNotEquals($admin1->id, $data['user_id']);
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function test_tienda_sin_administrador_asociado_no_devuelve_administrador_global(): void
    {
        $adminGlobal = User::factory()->create([
            'correo' => 'global@void.test',
            'rol' => 'administrador',
            'id_tienda' => null,
            'estado' => true,
        ]);

        $tiendaHuerfana = Tienda::create([
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
        $userInactivo = User::factory()->create([
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

        Mail::shouldReceive('raw')
            ->once()
            ->andThrow(new \Exception('SMTP connection timeout'));

        $response = $this->post(route('password.email'), [
            'correo' => 'admin.fallo@void.test',
        ]);

        $response->assertSessionHasErrors(['correo']);
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
}
