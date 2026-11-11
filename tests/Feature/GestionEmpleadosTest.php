<?php

use App\Models\Tienda;
use App\Models\User;
use Database\Seeders\SeguridadSeeder;

beforeEach(function () {
    $this->seed(SeguridadSeeder::class);
});

test('un administrador puede listar unicamente a los empleados de su tienda', function () {
    $tiendaA = Tienda::factory()->create(['estado' => true]);
    $tiendaB = Tienda::factory()->create(['estado' => true]);

    $adminA = User::factory()->create(['id_tienda' => $tiendaA->id, 'rol' => 'administrador', 'estado' => true]);
    $adminA->assignRole('administrador');

    $empA = User::factory()->create(['id_tienda' => $tiendaA->id, 'rol' => 'empleado', 'nombre' => 'Carlos Empleado A', 'estado' => true]);
    $empA->assignRole('empleado');

    $empB = User::factory()->create(['id_tienda' => $tiendaB->id, 'rol' => 'empleado', 'nombre' => 'Diana Empleado B', 'estado' => true]);
    $empB->assignRole('empleado');

    $response = $this->actingAs($adminA)->get(route('empleados.index'));

    $response->assertOk();
    $response->assertSee('Carlos Empleado A');
    $response->assertDontSee('Diana Empleado B');
});

test('un administrador puede registrar un nuevo empleado en su tienda', function () {
    $tienda = Tienda::factory()->create(['estado' => true]);
    $admin = User::factory()->create(['id_tienda' => $tienda->id, 'rol' => 'administrador', 'estado' => true]);
    $admin->assignRole('administrador');

    $datos = [
        'nombre' => 'Pedro',
        'apellido' => 'Gomez',
        'documento' => '1234567890',
        'correo' => 'pedro.gomez@tienda.com',
        'password' => 'secreto123',
        'estado' => 1,
    ];

    $response = $this->actingAs($admin)->post(route('empleados.store'), $datos);

    $response->assertRedirect(route('empleados.index'));

    $this->assertDatabaseHas('usuarios', [
        'documento' => '1234567890',
        'correo' => 'pedro.gomez@tienda.com',
        'id_tienda' => $tienda->id,
        'rol' => 'empleado',
        'estado' => 1,
    ]);

    $nuevoEmpleado = User::where('documento', '1234567890')->first();
    expect($nuevoEmpleado->hasRole('empleado'))->toBeTrue();
});

test('un administrador no puede editar ni manipular empleados de otra tienda', function () {
    $tiendaA = Tienda::factory()->create(['estado' => true]);
    $tiendaB = Tienda::factory()->create(['estado' => true]);

    $adminA = User::factory()->create(['id_tienda' => $tiendaA->id, 'rol' => 'administrador', 'estado' => true]);
    $adminA->assignRole('administrador');

    $empB = User::factory()->create(['id_tienda' => $tiendaB->id, 'rol' => 'empleado', 'estado' => true]);
    $empB->assignRole('empleado');

    // Intentar ver formulario de edición de empleado ajeno
    $this->actingAs($adminA)->get(route('empleados.edit', $empB->id))->assertForbidden();

    // Intentar actualizar empleado ajeno
    $this->actingAs($adminA)->put(route('empleados.update', $empB->id), [
        'nombre' => 'Hackeado',
        'apellido' => 'Modificado',
        'documento' => $empB->documento,
        'correo' => $empB->correo,
        'estado' => 1,
    ])->assertForbidden();

    // Intentar eliminar empleado ajeno
    $this->actingAs($adminA)->delete(route('empleados.destroy', $empB->id))->assertForbidden();
});

test('un empleado no tiene permiso para acceder a la gestion de empleados', function () {
    $tienda = Tienda::factory()->create(['estado' => true]);
    $emp = User::factory()->create(['id_tienda' => $tienda->id, 'rol' => 'empleado', 'estado' => true]);
    $emp->assignRole('empleado');

    $this->actingAs($emp)->get(route('empleados.index'))->assertForbidden();
    $this->actingAs($emp)->get(route('empleados.create'))->assertForbidden();
    $this->actingAs($emp)->post(route('empleados.store'), [])->assertForbidden();
});
