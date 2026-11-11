<?php

namespace Tests\Feature;

use App\Models\Tienda;
use App\Models\User;
use Database\Seeders\SeguridadSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistroTiendaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SeguridadSeeder::class);
    }

    public function test_visitante_puede_acceder_al_formulario_publico_de_registro(): void
    {
        $response = $this->get(route('registro.tienda'));

        $response->assertOk();
        $response->assertSee('Registrar tienda');
        $response->assertSee('Información de la Tienda');
        $response->assertSee('Cuenta del Administrador');
    }

    public function test_registro_correcto_de_tienda_y_administrador(): void
    {
        $payload = [
            'nombre_tienda' => 'Zapatería El Paso',
            'descripcion_tienda' => 'Venta de calzado deportivo y formal.',
            'direccion' => 'Calle 100 # 15-20',
            'telefono' => '3001234567',
            'correo_tienda' => 'contacto@elpaso.com',
            'nombre' => 'Carlos',
            'apellido' => 'Gómez',
            'documento' => '1020304050',
            'correo' => 'carlos.admin@elpaso.com',
            'password' => 'Secreto123*',
            'password_confirmation' => 'Secreto123*',
        ];

        $response = $this->post(route('registro.tienda.store'), $payload);

        $response->assertRedirect(route('dashboard.index'));
        $response->assertSessionHas('success');

        // Verificar que la tienda fue creada
        $this->assertDatabaseHas('tiendas', [
            'nombre' => 'Zapatería El Paso',
            'correo' => 'contacto@elpaso.com',
            'telefono' => '3001234567',
            'estado' => true,
        ]);

        $tienda = Tienda::where('nombre', 'Zapatería El Paso')->first();
        $this->assertNotNull($tienda);

        // Verificar que el usuario administrador fue creado y asociado a la tienda
        $this->assertDatabaseHas('usuarios', [
            'documento' => '1020304050',
            'correo' => 'carlos.admin@elpaso.com',
            'rol' => 'administrador',
            'id_tienda' => $tienda->id,
            'estado' => true,
        ]);

        $user = User::where('correo', 'carlos.admin@elpaso.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('Secreto123*', $user->password));
        $this->assertTrue($user->hasRole('administrador'));

        // Verificar que el usuario quedó autenticado
        $this->assertAuthenticatedAs($user);
    }

    public function test_registro_con_correo_duplicado(): void
    {
        User::factory()->create([
            'correo' => 'admin_existente@tienda.com',
            'documento' => '9988776655',
        ]);

        $payload = [
            'nombre_tienda' => 'Tienda Duplicada',
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'documento' => '1122334455',
            'correo' => 'admin_existente@tienda.com', // duplicado
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->post(route('registro.tienda.store'), $payload);

        $response->assertSessionHasErrors(['correo']);
        $this->assertDatabaseMissing('tiendas', ['nombre' => 'Tienda Duplicada']);
        $this->assertDatabaseMissing('usuarios', ['documento' => '1122334455']);
        $this->assertGuest();
    }

    public function test_registro_con_documento_duplicado(): void
    {
        User::factory()->create([
            'correo' => 'otro_admin@tienda.com',
            'documento' => '1234567890',
        ]);

        $payload = [
            'nombre_tienda' => 'Tienda Doc Duplicado',
            'nombre' => 'Ana',
            'apellido' => 'López',
            'documento' => '1234567890', // duplicado
            'correo' => 'ana.nueva@tienda.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $response = $this->post(route('registro.tienda.store'), $payload);

        $response->assertSessionHasErrors(['documento']);
        $this->assertDatabaseMissing('tiendas', ['nombre' => 'Tienda Doc Duplicado']);
        $this->assertDatabaseMissing('usuarios', ['correo' => 'ana.nueva@tienda.com']);
        $this->assertGuest();
    }

    public function test_fallo_durante_creacion_revierte_transaccion_completamente(): void
    {
        // Simular un fallo forzado dentro de la transacción, por ejemplo en User::creating
        User::creating(function ($user) {
            if ($user->correo === 'error_forzado@tienda.com') {
                throw new \Exception('Error simulado en la creación del usuario');
            }
        });

        $payload = [
            'nombre_tienda' => 'Tienda Que Debe Revertir',
            'nombre' => 'Pedro',
            'apellido' => 'Ramírez',
            'documento' => '5544332211',
            'correo' => 'error_forzado@tienda.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        try {
            $this->post(route('registro.tienda.store'), $payload);
        } catch (\Exception $e) {
            $this->assertSame('Error simulado en la creación del usuario', $e->getMessage());
        }

        // Verificar que la tienda NO quedó en la base de datos (rollback exitoso)
        $this->assertDatabaseMissing('tiendas', ['nombre' => 'Tienda Que Debe Revertir']);
        $this->assertDatabaseMissing('usuarios', ['documento' => '5544332211']);
        $this->assertGuest();
    }
}
