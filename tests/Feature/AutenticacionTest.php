<?php

use App\Models\User;
use Database\Seeders\SeguridadSeeder;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->seed(SeguridadSeeder::class);
});

test('un usuario con credenciales correctas puede iniciar sesion', function () {
    $user = User::factory()->create([
        'correo' => 'activo@example.com',
        'password' => bcrypt('password123'),
        'estado' => true,
        'rol' => 'administrador',
    ]);
    $user->assignRole('administrador');

    $response = $this->post(route('login.store'), [
        'correo' => 'activo@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('dashboard.index'));
    $this->assertAuthenticatedAs($user);
});

test('un usuario con credenciales incorrectas no puede ingresar', function () {
    User::factory()->create([
        'correo' => 'activo@example.com',
        'password' => bcrypt('password123'),
        'estado' => true,
        'rol' => 'administrador',
    ]);

    $response = $this->post(route('login.store'), [
        'correo' => 'activo@example.com',
        'password' => 'clave-erronea',
    ]);

    $response->assertSessionHasErrors('correo');
    $this->assertGuest();
});

test('un usuario inactivo no puede iniciar sesion aunque sus credenciales sean correctas', function () {
    $user = User::factory()->create([
        'correo' => 'inactivo@example.com',
        'password' => bcrypt('password123'),
        'estado' => false,
        'rol' => 'empleado',
    ]);
    $user->assignRole('empleado');

    $response = $this->post(route('login.store'), [
        'correo' => 'inactivo@example.com',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors('correo');
    $this->assertGuest();
});

test('recordar sesion funciona correctamente', function () {
    $user = User::factory()->create([
        'correo' => 'recuerdame@example.com',
        'password' => bcrypt('password123'),
        'estado' => true,
        'rol' => 'administrador',
    ]);

    $response = $this->post(route('login.store'), [
        'correo' => 'recuerdame@example.com',
        'password' => 'password123',
        'remember' => '1',
    ]);

    $response->assertRedirect(route('dashboard.index'));
    $this->assertAuthenticatedAs($user);
    $response->assertCookie(Auth::guard()->getRecallerName());
});

test('cerrar sesion invalida correctamente la sesion', function () {
    $user = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
    ]);

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('inicio'));
    $this->assertGuest();
});

test('un visitante no puede acceder al dashboard ni rutas protegidas', function () {
    $response = $this->get(route('dashboard.index'));
    $response->assertRedirect(route('login'));

    $responseTiendas = $this->get(route('tiendas.index'));
    $responseTiendas->assertRedirect(route('login'));
});
