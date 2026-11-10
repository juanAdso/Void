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
        $admin = User::factory()->create([
            'correo' => 'admin_tienda@void.test',
            'rol' => 'administrador',
            'estado' => true,
        ]);

        $tienda = Tienda::create([
            'nombre' => 'Tienda Central',
            'correo' => 'contacto@tiendacentral.com',
            'estado' => true,
        ]);

        $response = $this->post(route('password.email'), [
            'correo' => 'contacto@tiendacentral.com',
        ]);

        $response->assertRedirect(route('password.code'));
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
