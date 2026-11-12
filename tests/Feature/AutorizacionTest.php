<?php

use App\Models\Facturas;
use App\Models\Productos;
use App\Models\Tienda;
use App\Models\User;
use Database\Seeders\SeguridadSeeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(SeguridadSeeder::class);
});

test('administrador tiene acceso a todos los modulos y operaciones', function () {
    $tienda = Tienda::factory()->create();
    $admin = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
        'id_tienda' => $tienda->id,
    ]);
    $admin->assignRole('administrador');

    // Dashboard
    $this->actingAs($admin)->get(route('dashboard.index'))->assertOk();

    // Tiendas
    $this->actingAs($admin)->get(route('tiendas.index'))->assertOk();
    $this->actingAs($admin)->get(route('tiendas.create'))->assertForbidden();

    // Categorias
    $this->actingAs($admin)->get(route('categoria.index'))->assertOk();
    $this->actingAs($admin)->get(route('categoria.create'))->assertOk();

    // Clientes
    $this->actingAs($admin)->get(route('cliente.index'))->assertOk();
    $this->actingAs($admin)->get(route('cliente.create'))->assertOk();

    // Productos
    $this->actingAs($admin)->get(route('productos.index'))->assertOk();
    $this->actingAs($admin)->get(route('productos.create'))->assertOk();

    // Facturas
    $this->actingAs($admin)->get(route('facturas.index'))->assertOk();
    $this->actingAs($admin)->get(route('facturas.create'))->assertOk();

    // Detalle Facturas
    $this->actingAs($admin)->get(route('detalle_facturas.index'))->assertOk();
    $this->actingAs($admin)->get(route('detalle_facturas.create'))->assertOk();
});

test('empleado tiene acceso solo a los modulos y operaciones autorizadas', function () {
    $tienda = Tienda::factory()->create();
    $empleado = User::factory()->create([
        'estado' => true,
        'rol' => 'empleado',
        'id_tienda' => $tienda->id,
    ]);
    $empleado->assignRole('empleado');

    // Permitidos:
    // Dashboard
    $this->actingAs($empleado)->get(route('dashboard.index'))->assertOk();

    // Ver categorias, productos, clientes, facturas, detalle facturas
    $this->actingAs($empleado)->get(route('categoria.index'))->assertOk();
    $this->actingAs($empleado)->get(route('productos.index'))->assertOk();
    $this->actingAs($empleado)->get(route('cliente.index'))->assertOk();
    $this->actingAs($empleado)->get(route('facturas.index'))->assertOk();
    $this->actingAs($empleado)->get(route('facturas.create'))->assertOk();
    $this->actingAs($empleado)->get(route('detalle_facturas.index'))->assertOk();
    $this->actingAs($empleado)->get(route('detalle_facturas.create'))->assertOk();

    // Denegados (403):
    // Tiendas (no tiene permiso)
    $this->actingAs($empleado)->get(route('tiendas.index'))->assertForbidden();
    $this->actingAs($empleado)->get(route('tiendas.create'))->assertForbidden();

    // Crear categorias, clientes, productos
    $this->actingAs($empleado)->get(route('categoria.create'))->assertForbidden();
    $this->actingAs($empleado)->get(route('cliente.create'))->assertForbidden();
    $this->actingAs($empleado)->get(route('productos.create'))->assertForbidden();

    // Eliminar registros
    $this->actingAs($empleado)->delete('/categoria/1')->assertForbidden();
    $this->actingAs($empleado)->delete('/cliente/1')->assertForbidden();
    $this->actingAs($empleado)->delete('/productos/1')->assertForbidden();
    $this->actingAs($empleado)->delete('/facturas/1')->assertForbidden();
    $this->actingAs($empleado)->delete('/detalle_facturas/1')->assertForbidden();
    $this->actingAs($empleado)->delete('/tiendas/1')->assertForbidden();
});

test('el catalogo publico sigue accesible sin iniciar sesion', function () {
    $responseInicio = $this->get(route('inicio'));
    $responseInicio->assertOk();

    $responseCatalogo = $this->get(route('catalogo'));
    $responseCatalogo->assertOk();
});

test('ejecutar seguridad seeder multiples veces no duplica roles ni permisos', function () {
    $this->seed(SeguridadSeeder::class);
    $this->seed(SeguridadSeeder::class);

    expect(Role::where('name', 'administrador')->count())->toBe(1);
    expect(Role::where('name', 'empleado')->count())->toBe(1);
    expect(Role::count())->toBe(2);
    expect(Permission::where('name', 'ver dashboard')->count())->toBe(1);
});

test('el formulario de registro de tienda es accesible publicamente mediante la ruta registro.tienda', function () {
    $response = $this->get(route('registro.tienda'));
    $response->assertOk();
    $response->assertSee('Registrar tienda');
});


test('el rol administrador no evita la comprobacion de permisos granulares', function () {
    $tienda = Tienda::factory()->create();
    $admin = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
        'id_tienda' => $tienda->id,
    ]);
    $admin->assignRole('administrador');
    $admin->revokePermissionTo('ver clientes');

    $this->actingAs($admin)->get(route('cliente.index'))->assertForbidden();
});
