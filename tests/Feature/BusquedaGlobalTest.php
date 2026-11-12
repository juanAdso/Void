<?php

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Facturas;
use App\Models\Productos;
use App\Models\Tienda;
use App\Models\User;
use Database\Seeders\SeguridadSeeder;

beforeEach(function () {
    $this->seed(SeguridadSeeder::class);
});

test('el buscador global encuentra registros de los seis modulos habilitados', function () {
    $tienda = Tienda::factory()->create(['nombre' => 'Centinela Store']);

    $admin = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
        'id_tienda' => $tienda->id,
    ]);
    $admin->assignRole('administrador');

    $categoria = Categoria::create([
        'id_tienda' => $tienda->id,
        'nombre' => 'Centinela',
        'descripcion' => 'Categoría Centinela',
        'estado' => true,
    ]);

    Productos::create([
        'id_tienda' => $tienda->id,
        'id_categoria' => $categoria->id,
        'nombre' => 'Producto Centinela',
        'descripcion' => 'Artículo de prueba',
        'precio' => 25000,
        'stock' => 12,
        'estado' => true,
    ]);

    $cliente = Cliente::create([
        'id_tienda' => $tienda->id,
        'nombre' => 'Centinela',
        'apellido' => 'Cliente',
        'documento' => '1234567890',
        'correo' => 'centinela.cliente@example.test',
        'telefono' => '3001234567',
        'direccion' => 'Dirección de prueba',
    ]);

    Facturas::create([
        'id_cliente' => $cliente->id,
        'id_usuario' => $admin->id,
        'fecha' => '2026-11-12',
        'subtotal' => 25000,
        'iva' => 0,
        'total' => 25000,
        'estado' => 'pendiente',
    ]);

    $empleado = User::factory()->create([
        'nombre' => 'Centinela',
        'apellido' => 'Empleado',
        'documento' => '9876543210',
        'correo' => 'centinela.empleado@example.test',
        'estado' => true,
        'rol' => 'empleado',
        'id_tienda' => $tienda->id,
    ]);
    $empleado->assignRole('empleado');

    $response = $this->actingAs($admin)->get(route('busqueda.global', ['q' => 'Centinela']));

    $response->assertOk()
        ->assertSee('Productos')
        ->assertSee('Categorías')
        ->assertSee('Clientes')
        ->assertSee('Facturas')
        ->assertSee('Empleados')
        ->assertSee('Tiendas')
        ->assertSee('Producto Centinela')
        ->assertSee('Centinela Cliente')
        ->assertSee('Centinela Empleado')
        ->assertSee('Centinela Store');
});

test('el buscador nunca muestra datos pertenecientes a otra tienda', function () {
    $tiendaA = Tienda::factory()->create(['nombre' => 'Tienda A']);
    $tiendaB = Tienda::factory()->create(['nombre' => 'Tienda Privada']);

    $adminA = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
        'id_tienda' => $tiendaA->id,
    ]);
    $adminA->assignRole('administrador');

    $adminB = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
        'id_tienda' => $tiendaB->id,
    ]);
    $adminB->assignRole('administrador');

    $categoriaB = Categoria::create([
        'id_tienda' => $tiendaB->id,
        'nombre' => 'Categoría Privada',
        'descripcion' => 'Información privada',
        'estado' => true,
    ]);

    Productos::create([
        'id_tienda' => $tiendaB->id,
        'id_categoria' => $categoriaB->id,
        'nombre' => 'Producto Privado',
        'descripcion' => 'Registro privado',
        'precio' => 10000,
        'stock' => 5,
        'estado' => true,
    ]);

    $clienteB = Cliente::create([
        'id_tienda' => $tiendaB->id,
        'nombre' => 'Cliente Privado',
        'apellido' => 'Reservado',
        'documento' => '1112223334',
        'correo' => 'privado@example.test',
    ]);

    Facturas::create([
        'id_cliente' => $clienteB->id,
        'id_usuario' => $adminB->id,
        'fecha' => '2026-11-12',
        'subtotal' => 10000,
        'iva' => 0,
        'total' => 10000,
        'estado' => 'pendiente',
    ]);

    $response = $this->actingAs($adminA)->get(route('busqueda.global', ['q' => 'Privado']));

    $response->assertOk()
        ->assertDontSee('Producto Privado')
        ->assertDontSee('Categoría Privada')
        ->assertDontSee('Cliente Privado')
        ->assertDontSee('Tienda Privada');
});

test('un empleado solo obtiene resultados de los modulos que tiene permitidos', function () {
    $tienda = Tienda::factory()->create(['nombre' => 'Tienda restringida']);

    $empleado = User::factory()->create([
        'nombre' => 'Empleado',
        'apellido' => 'Autorizado',
        'estado' => true,
        'rol' => 'empleado',
        'id_tienda' => $tienda->id,
    ]);
    $empleado->assignRole('empleado');

    $categoria = Categoria::create([
        'id_tienda' => $tienda->id,
        'nombre' => 'Categoría autorizada',
        'descripcion' => null,
        'estado' => true,
    ]);

    Productos::create([
        'id_tienda' => $tienda->id,
        'id_categoria' => $categoria->id,
        'nombre' => 'Producto Restringido',
        'descripcion' => 'Coincidencia de prueba',
        'precio' => 10000,
        'stock' => 3,
        'estado' => true,
    ]);

    User::factory()->create([
        'nombre' => 'Empleado Restringido',
        'apellido' => 'Privado',
        'documento' => '5555555555',
        'correo' => 'privado.empleado@example.test',
        'estado' => true,
        'rol' => 'empleado',
        'id_tienda' => $tienda->id,
    ]);

    $response = $this->actingAs($empleado)->get(route('busqueda.global', ['q' => 'Restringido']));

    $response->assertOk()
        ->assertSee('Producto Restringido')
        ->assertDontSee('Empleado Restringido')
        ->assertDontSee('Tiendas');
});

test('el buscador exige autenticacion y al menos dos caracteres para consultar', function () {
    $this->get(route('busqueda.global'))->assertRedirect(route('login'));

    $tienda = Tienda::factory()->create();
    $admin = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
        'id_tienda' => $tienda->id,
    ]);
    $admin->assignRole('administrador');

    $this->actingAs($admin)
        ->get(route('busqueda.global', ['q' => 'a']))
        ->assertOk()
        ->assertSee('Escribe al menos dos caracteres');
});
