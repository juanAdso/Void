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

test('el superadministrador puede suspender y reactivar una empresa desde su panel', function () {
    $superadmin = User::factory()->create([
        'rol' => 'superadministrador',
        'id_tienda' => null,
        'estado' => true,
    ]);
    $superadmin->assignRole('superadministrador');

    $tienda = Tienda::factory()->create(['estado' => true]);
    $urlDetalle = route('superadmin.empresas.show', $tienda);

    $this->from($urlDetalle)
        ->actingAs($superadmin)
        ->patch(route('superadmin.empresas.estado', $tienda), ['estado' => 0])
        ->assertRedirect($urlDetalle);

    expect($tienda->fresh()->estado)->toBeFalse();

    $this->from($urlDetalle)
        ->actingAs($superadmin)
        ->patch(route('superadmin.empresas.estado', $tienda), ['estado' => 1])
        ->assertRedirect($urlDetalle);

    expect($tienda->fresh()->estado)->toBeTrue();
});

test('el superadministrador puede activar y desactivar cuentas de una empresa', function () {
    $superadmin = User::factory()->create([
        'rol' => 'superadministrador',
        'id_tienda' => null,
        'estado' => true,
    ]);
    $superadmin->assignRole('superadministrador');

    $tienda = Tienda::factory()->create(['estado' => true]);
    $administrador = User::factory()->create([
        'rol' => 'administrador',
        'id_tienda' => $tienda->id,
        'estado' => true,
    ]);
    $administrador->assignRole('administrador');

    $empleado = User::factory()->create([
        'rol' => 'empleado',
        'id_tienda' => $tienda->id,
        'estado' => true,
    ]);
    $empleado->assignRole('empleado');

    $urlDetalle = route('superadmin.empresas.show', $tienda);

    $this->from($urlDetalle)
        ->actingAs($superadmin)
        ->patch(route('superadmin.empresas.usuarios.estado', [$tienda, $empleado]), ['estado' => 0])
        ->assertRedirect($urlDetalle);

    expect($empleado->fresh()->estado)->toBeFalse();

    $this->from($urlDetalle)
        ->actingAs($superadmin)
        ->patch(route('superadmin.empresas.usuarios.estado', [$tienda, $empleado]), ['estado' => 1])
        ->assertRedirect($urlDetalle);

    expect($empleado->fresh()->estado)->toBeTrue();
});

test('el superadministrador no puede desactivar al ultimo administrador activo de una empresa', function () {
    $superadmin = User::factory()->create([
        'rol' => 'superadministrador',
        'id_tienda' => null,
        'estado' => true,
    ]);
    $superadmin->assignRole('superadministrador');

    $tienda = Tienda::factory()->create(['estado' => true]);
    $administrador = User::factory()->create([
        'rol' => 'administrador',
        'id_tienda' => $tienda->id,
        'estado' => true,
    ]);
    $administrador->assignRole('administrador');

    $this->actingAs($superadmin)
        ->patch(route('superadmin.empresas.usuarios.estado', [$tienda, $administrador]), ['estado' => 0])
        ->assertSessionHasErrors('estado');

    expect($administrador->fresh()->estado)->toBeTrue();
});

test('el superadministrador no puede cambiar el estado de cuentas de otra empresa', function () {
    $superadmin = User::factory()->create([
        'rol' => 'superadministrador',
        'id_tienda' => null,
        'estado' => true,
    ]);
    $superadmin->assignRole('superadministrador');

    $tiendaA = Tienda::factory()->create(['estado' => true]);
    $tiendaB = Tienda::factory()->create(['estado' => true]);
    $empleadoB = User::factory()->create([
        'rol' => 'empleado',
        'id_tienda' => $tiendaB->id,
        'estado' => true,
    ]);
    $empleadoB->assignRole('empleado');

    $this->actingAs($superadmin)
        ->patch(route('superadmin.empresas.usuarios.estado', [$tiendaA, $empleadoB]), ['estado' => 0])
        ->assertNotFound();

    expect($empleadoB->fresh()->estado)->toBeTrue();
});
