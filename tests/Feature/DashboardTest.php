<?php

use App\Models\Tienda;
use App\Models\User;
use Database\Seeders\SeguridadSeeder;

beforeEach(function () {
    $this->seed(SeguridadSeeder::class);
});

test('administrador sin tienda asignada no puede acceder al dashboard ni obtener datos globales', function () {
    $admin = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
        'id_tienda' => null,
    ]);
    $admin->assignRole('administrador');

    $response = $this->actingAs($admin)->get(route('dashboard.index'));

    $response->assertForbidden();
});

test('administrador con tienda asignada puede cargar el dashboard', function () {
    $tienda = Tienda::factory()->create();
    $admin = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
        'id_tienda' => $tienda->id,
    ]);
    $admin->assignRole('administrador');

    $response = $this->actingAs($admin)->get(route('dashboard.index'));

    $response->assertOk();
    $response->assertViewIs('dashboard.admin');
    expect($admin->tienda->id)->toBe($tienda->id);
    expect($tienda->usuarios()->pluck('id')->all())->toContain($admin->id);
});

test('empleado puede cargar el dashboard de su tienda', function () {
    $tienda = Tienda::factory()->create();
    $empleado = User::factory()->create([
        'estado' => true,
        'rol' => 'empleado',
        'id_tienda' => $tienda->id,
    ]);
    $empleado->assignRole('empleado');

    $response = $this->actingAs($empleado)->get(route('dashboard.index'));

    $response->assertOk();
    $response->assertViewIs('dashboard.empleado');
    $response->assertViewHasAll([
        'ventasHoy',
        'ingresosHoy',
        'productosTienda',
        'facturasRecientes',
    ]);
});
