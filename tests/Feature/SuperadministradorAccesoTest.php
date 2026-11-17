<?php

use App\Models\Tienda;
use App\Models\User;
use Database\Seeders\SeguridadSeeder;

beforeEach(function () {
    $this->seed(SeguridadSeeder::class);
});

test('el superadministrador global entra al centro de control sin pertenecer a una tienda', function () {
    $superadmin = User::factory()->create([
        'rol' => 'superadministrador',
        'id_tienda' => null,
        'estado' => true,
    ]);
    $superadmin->assignRole('superadministrador');

    $this->actingAs($superadmin)
        ->get(route('superadmin.dashboard'))
        ->assertOk();
});

test('un administrador de empresa no puede entrar al panel global', function () {
    $tienda = Tienda::factory()->create();
    $admin = User::factory()->create([
        'rol' => 'administrador',
        'id_tienda' => $tienda->id,
        'estado' => true,
    ]);
    $admin->assignRole('administrador');

    $this->actingAs($admin)
        ->get(route('superadmin.dashboard'))
        ->assertForbidden();
});

test('un empleado no puede entrar al panel global', function () {
    $tienda = Tienda::factory()->create();
    $empleado = User::factory()->create([
        'rol' => 'empleado',
        'id_tienda' => $tienda->id,
        'estado' => true,
    ]);
    $empleado->assignRole('empleado');

    $this->actingAs($empleado)
        ->get(route('superadmin.empresas.index'))
        ->assertForbidden();
});

test('el superadministrador puede ver empresas y crear otro administrador asociado a una empresa', function () {
    $superadmin = User::factory()->create([
        'rol' => 'superadministrador',
        'id_tienda' => null,
        'estado' => true,
    ]);
    $superadmin->assignRole('superadministrador');

    $tienda = Tienda::factory()->create();

    $this->actingAs($superadmin)
        ->get(route('superadmin.empresas.index'))
        ->assertOk();

    $this->actingAs($superadmin)
        ->post(route('superadmin.empresas.administradores.store', $tienda), [
            'nombre' => 'Administrador',
            'apellido' => 'Nuevo',
            'documento' => 'SUPER-ADMIN-001',
            'correo' => 'admin.nuevo@empresa.test',
            'password' => 'ClaveSegura2026!',
            'password_confirmation' => 'ClaveSegura2026!',
        ])
        ->assertRedirect(route('superadmin.empresas.show', $tienda));

    $usuario = User::where('correo', 'admin.nuevo@empresa.test')->firstOrFail();

    expect($usuario->rol)->toBe('administrador')
        ->and((int) $usuario->id_tienda)->toBe((int) $tienda->id)
        ->and($usuario->hasRole('administrador'))->toBeTrue();
});
