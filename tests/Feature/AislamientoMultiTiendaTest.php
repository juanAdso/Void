<?php

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\DetalleFactura;
use App\Models\Facturas;
use App\Models\Productos;
use App\Models\Tienda;
use App\Models\User;
use Database\Seeders\SeguridadSeeder;

beforeEach(function () {
    $this->seed(SeguridadSeeder::class);
});

test('una tienda puede consultar sus propios productos', function () {
    $tiendaA = Tienda::factory()->create();
    $adminA = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
        'id_tienda' => $tiendaA->id,
    ]);
    $adminA->assignRole('administrador');

    $categoria = Categoria::factory()->create(['id_tienda' => $tiendaA->id]);
    $productoA = Productos::factory()->create([
        'id_tienda' => $tiendaA->id,
        'id_categoria' => $categoria->id,
        'nombre' => 'Producto Tienda A',
    ]);

    $response = $this->actingAs($adminA)->get(route('productos.index'));
    $response->assertOk();
    $response->assertSee('Producto Tienda A');
});

test('una tienda no puede consultar productos de otra tienda en el listado ni en edicion', function () {
    $tiendaA = Tienda::factory()->create();
    $tiendaB = Tienda::factory()->create();

    $adminA = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
        'id_tienda' => $tiendaA->id,
    ]);
    $adminA->assignRole('administrador');

    $categoria = Categoria::factory()->create(['id_tienda' => $tiendaB->id]);
    $productoB = Productos::factory()->create([
        'id_tienda' => $tiendaB->id,
        'id_categoria' => $categoria->id,
        'nombre' => 'Producto Secreto Tienda B',
    ]);

    // En el index de A no debe verse el producto de B
    $responseIndex = $this->actingAs($adminA)->get(route('productos.index'));
    $responseIndex->assertOk();
    $responseIndex->assertDontSee('Producto Secreto Tienda B');

    // Intentar acceder a la vista de edición del producto de B por ID en la URL debe fallar (404)
    $responseEdit = $this->actingAs($adminA)->get(route('productos.edit', $productoB->id));
    $responseEdit->assertNotFound();
});

test('una tienda no puede editar productos ajenos manipulando el ID en la URL', function () {
    $tiendaA = Tienda::factory()->create();
    $tiendaB = Tienda::factory()->create();

    $adminA = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
        'id_tienda' => $tiendaA->id,
    ]);
    $adminA->assignRole('administrador');

    $categoria = Categoria::factory()->create(['id_tienda' => $tiendaB->id]);
    $productoB = Productos::factory()->create([
        'id_tienda' => $tiendaB->id,
        'id_categoria' => $categoria->id,
        'nombre' => 'Original B',
        'precio' => 100,
    ]);

    $response = $this->actingAs($adminA)->put(route('productos.update', $productoB->id), [
        'nombre' => 'Hackeado por A',
        'descripcion' => 'Desc',
        'precio' => 10,
        'stock' => 5,
        'estado' => 1,
        'id_categoria' => $categoria->id,
    ]);

    $response->assertForbidden();
    expect($productoB->fresh()->nombre)->toBe('Original B');
});

test('una tienda no puede eliminar productos ajenos', function () {
    $tiendaA = Tienda::factory()->create();
    $tiendaB = Tienda::factory()->create();

    $adminA = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
        'id_tienda' => $tiendaA->id,
    ]);
    $adminA->assignRole('administrador');

    $categoria = Categoria::factory()->create(['id_tienda' => $tiendaB->id]);
    $productoB = Productos::factory()->create([
        'id_tienda' => $tiendaB->id,
        'id_categoria' => $categoria->id,
    ]);

    $response = $this->actingAs($adminA)->delete(route('productos.destroy', $productoB->id));
    $response->assertNotFound();

    expect(Productos::where('id', $productoB->id)->exists())->toBeTrue();
});

test('crear producto asigna siempre la tienda del usuario autenticado e ignora id_tienda manipulado', function () {
    $tiendaA = Tienda::factory()->create();
    $tiendaB = Tienda::factory()->create();

    $adminA = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
        'id_tienda' => $tiendaA->id,
    ]);
    $adminA->assignRole('administrador');

    $categoria = Categoria::factory()->create(['id_tienda' => $tiendaA->id]);

    $this->actingAs($adminA)->post(route('productos.store'), [
        'nombre' => 'Nuevo Producto Seguro',
        'descripcion' => 'Desc',
        'precio' => 50,
        'stock' => 10,
        'estado' => 1,
        'id_categoria' => $categoria->id,
        'id_tienda' => $tiendaB->id, // Intento de inyectar Tienda B
    ])->assertRedirect(route('productos.index'));

    $productoCreado = Productos::where('nombre', 'Nuevo Producto Seguro')->first();
    expect($productoCreado)->not->toBeNull();
    expect($productoCreado->id_tienda)->toBe($tiendaA->id);
    expect($productoCreado->id_tienda)->not->toBe($tiendaB->id);
});

test('una tienda no puede consultar ni editar facturas ajenas', function () {
    $tiendaA = Tienda::factory()->create();
    $tiendaB = Tienda::factory()->create();

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

    $cliente = Cliente::factory()->create(['id_tienda' => $tiendaB->id]);
    $facturaB = Facturas::create([
        'id_cliente' => $cliente->id,
        'id_usuario' => $adminB->id,
        'fecha' => '2026-03-01',
        'subtotal' => 100,
        'iva' => 19,
        'total' => 119,
        'estado' => 'pendiente',
    ]);

    // Admin A no debe ver la factura B en index
    $responseIndex = $this->actingAs($adminA)->get(route('facturas.index'));
    $responseIndex->assertOk();
    $responseIndex->assertDontSee('Factura #'.$facturaB->id);
    $responseIndex->assertDontSee(route('facturas.edit', $facturaB->id));

    // Admin A no debe poder editar factura B
    $this->actingAs($adminA)->get(route('facturas.edit', $facturaB->id))->assertNotFound();

    // Admin A no debe poder hacer update a factura B
    $this->actingAs($adminA)->put(route('facturas.update', $facturaB->id), [
        'id_cliente' => $cliente->id,
        'fecha' => '2026-03-02',
        'estado' => 'pagada',
    ])->assertForbidden();

    // Admin A no debe poder eliminar factura B
    $this->actingAs($adminA)->delete(route('facturas.destroy', $facturaB->id))->assertNotFound();

    expect($facturaB->fresh()->estado)->toBe('pendiente');
});

test('una tienda no puede agregar productos de otra tienda a sus facturas', function () {
    $tiendaA = Tienda::factory()->create();
    $tiendaB = Tienda::factory()->create();

    $adminA = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
        'id_tienda' => $tiendaA->id,
    ]);
    $adminA->assignRole('administrador');

    $cliente = Cliente::factory()->create(['id_tienda' => $tiendaA->id]);
    $categoria = Categoria::factory()->create(['id_tienda' => $tiendaB->id]);

    $facturaA = Facturas::create([
        'id_cliente' => $cliente->id,
        'id_usuario' => $adminA->id,
        'fecha' => '2026-03-01',
        'subtotal' => 0,
        'iva' => 0,
        'total' => 0,
        'estado' => 'pendiente',
    ]);

    $productoB = Productos::factory()->create([
        'id_tienda' => $tiendaB->id,
        'id_categoria' => $categoria->id,
        'stock' => 50,
        'precio' => 200,
    ]);

    $response = $this->actingAs($adminA)->post(route('detalle_facturas.store'), [
        'id_factura' => $facturaA->id,
        'id_producto' => $productoB->id,
        'cantidad' => 2,
        'precio_unitario' => 200,
    ]);

    $response->assertSessionHasErrors(['id_producto']);
    expect(DetalleFactura::where('id_factura', $facturaA->id)->count())->toBe(0);
});

test('una tienda no puede asociar detalles a facturas de otra tienda', function () {
    $tiendaA = Tienda::factory()->create();
    $tiendaB = Tienda::factory()->create();

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

    $cliente = Cliente::factory()->create(['id_tienda' => $tiendaB->id]);
    $categoria = Categoria::factory()->create(['id_tienda' => $tiendaA->id]);

    $facturaB = Facturas::create([
        'id_cliente' => $cliente->id,
        'id_usuario' => $adminB->id,
        'fecha' => '2026-03-01',
        'subtotal' => 0,
        'iva' => 0,
        'total' => 0,
        'estado' => 'pendiente',
    ]);

    $productoA = Productos::factory()->create([
        'id_tienda' => $tiendaA->id,
        'id_categoria' => $categoria->id,
        'stock' => 50,
        'precio' => 50,
    ]);

    $response = $this->actingAs($adminA)->post(route('detalle_facturas.store'), [
        'id_factura' => $facturaB->id,
        'id_producto' => $productoA->id,
        'cantidad' => 1,
        'precio_unitario' => 50,
    ]);

    $response->assertSessionHasErrors(['id_factura']);
    expect(DetalleFactura::where('id_factura', $facturaB->id)->count())->toBe(0);
});

test('una tienda no puede consultar ni modificar detalles de factura ajenos', function () {
    $tiendaA = Tienda::factory()->create();
    $tiendaB = Tienda::factory()->create();

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

    $cliente = Cliente::factory()->create(['id_tienda' => $tiendaB->id]);
    $categoria = Categoria::factory()->create(['id_tienda' => $tiendaB->id]);

    $facturaB = Facturas::create([
        'id_cliente' => $cliente->id,
        'id_usuario' => $adminB->id,
        'fecha' => '2026-03-01',
        'subtotal' => 100,
        'iva' => 19,
        'total' => 119,
        'estado' => 'pendiente',
    ]);

    $productoB = Productos::factory()->create([
        'id_tienda' => $tiendaB->id,
        'id_categoria' => $categoria->id,
        'stock' => 50,
        'precio' => 100,
    ]);

    $detalleB = DetalleFactura::create([
        'id_factura' => $facturaB->id,
        'id_producto' => $productoB->id,
        'cantidad' => 1,
        'precio_unitario' => 100,
        'subtotal' => 100,
    ]);

    // Admin A intentando consultar o editar detalle de B
    $this->actingAs($adminA)->get(route('detalle_facturas.edit', $detalleB->id))->assertNotFound();

    $responseUpdate = $this->actingAs($adminA)->put(route('detalle_facturas.update', $detalleB->id), [
        'id_factura' => $facturaB->id,
        'id_producto' => $productoB->id,
        'cantidad' => 2,
        'precio_unitario' => 100,
    ]);
    expect(in_array($responseUpdate->status(), [403, 404]))->toBeTrue();

    $this->actingAs($adminA)->delete(route('detalle_facturas.destroy', $detalleB->id))->assertNotFound();

    expect(DetalleFactura::where('id', $detalleB->id)->exists())->toBeTrue();
});

test('un empleado no puede superar los permisos asignados de su tienda', function () {
    $tienda = Tienda::factory()->create();
    $empleado = User::factory()->create([
        'estado' => true,
        'rol' => 'empleado',
        'id_tienda' => $tienda->id,
    ]);
    $empleado->assignRole('empleado');

    $categoria = Categoria::factory()->create(['id_tienda' => $tienda->id]);
    $producto = Productos::factory()->create([
        'id_tienda' => $tienda->id,
        'id_categoria' => $categoria->id,
    ]);

    // El empleado no tiene permiso para crear o eliminar productos
    $this->actingAs($empleado)->post(route('productos.store'), [
        'nombre' => 'Test',
        'precio' => 10,
        'stock' => 5,
        'estado' => 1,
        'id_categoria' => $categoria->id,
    ])->assertForbidden();

    $this->actingAs($empleado)->delete(route('productos.destroy', $producto->id))->assertForbidden();

    // El empleado no tiene permiso para administrar tiendas
    $this->actingAs($empleado)->get(route('tiendas.index'))->assertForbidden();
});

test('un usuario con tienda asignada no puede acceder ni modificar tiendas ajenas', function () {
    $tiendaA = Tienda::factory()->create();
    $tiendaB = Tienda::factory()->create(['nombre' => 'Tienda B Original']);

    $adminA = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
        'id_tienda' => $tiendaA->id,
    ]);
    $adminA->assignRole('administrador');

    // Intentar ver show de Tienda B
    $this->actingAs($adminA)->get(route('tiendas.show', $tiendaB->id))->assertNotFound();

    // Intentar ver edit de Tienda B
    $this->actingAs($adminA)->get(route('tiendas.edit', $tiendaB->id))->assertNotFound();

    // Intentar actualizar Tienda B
    $this->actingAs($adminA)->put(route('tiendas.update', $tiendaB->id), [
        'nombre' => 'Tienda B Hackeada',
        'estado' => 1,
    ])->assertNotFound();

    expect($tiendaB->fresh()->nombre)->toBe('Tienda B Original');
});


test('una tienda no puede consultar, modificar ni eliminar clientes de otra tienda', function () {
    $tiendaA = Tienda::factory()->create();
    $tiendaB = Tienda::factory()->create();

    $adminA = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
        'id_tienda' => $tiendaA->id,
    ]);
    $adminA->assignRole('administrador');

    $clienteB = Cliente::factory()->create([
        'id_tienda' => $tiendaB->id,
        'nombre' => 'Cliente Privado B',
    ]);

    $index = $this->actingAs($adminA)->get(route('cliente.index'));
    $index->assertOk();
    $index->assertDontSee('Cliente Privado B');

    $this->actingAs($adminA)->get(route('cliente.edit', $clienteB->id))->assertNotFound();

    $this->actingAs($adminA)->put(route('cliente.update', $clienteB->id), [
        'nombre' => 'Cliente alterado',
        'apellido' => 'Ajeno',
        'documento' => '9999999999',
    ])->assertNotFound();

    $this->actingAs($adminA)->delete(route('cliente.destroy', $clienteB->id))->assertNotFound();

    expect($clienteB->fresh()->nombre)->toBe('Cliente Privado B');
});

test('una tienda no puede consultar ni modificar categorias de otra tienda', function () {
    $tiendaA = Tienda::factory()->create();
    $tiendaB = Tienda::factory()->create();

    $adminA = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
        'id_tienda' => $tiendaA->id,
    ]);
    $adminA->assignRole('administrador');

    $categoriaB = Categoria::factory()->create([
        'id_tienda' => $tiendaB->id,
        'nombre' => 'Categoria Privada B',
    ]);

    $index = $this->actingAs($adminA)->get(route('categoria.index'));
    $index->assertOk();
    $index->assertDontSee('Categoria Privada B');

    $this->actingAs($adminA)->get(route('categoria.edit', $categoriaB->id))->assertNotFound();
    $this->actingAs($adminA)->delete(route('categoria.destroy', $categoriaB->id))->assertNotFound();

    expect($categoriaB->fresh()->nombre)->toBe('Categoria Privada B');
});

test('crear cliente siempre asigna la tienda autenticada aunque se manipule id_tienda', function () {
    $tiendaA = Tienda::factory()->create();
    $tiendaB = Tienda::factory()->create();
    $adminA = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
        'id_tienda' => $tiendaA->id,
    ]);
    $adminA->assignRole('administrador');

    $this->actingAs($adminA)->post(route('cliente.store'), [
        'nombre' => 'Cliente Seguro',
        'apellido' => 'A',
        'documento' => '2012345678',
        'id_tienda' => $tiendaB->id,
    ])->assertRedirect(route('cliente.index'));

    $cliente = Cliente::where('documento', '2012345678')->firstOrFail();
    expect((int) $cliente->id_tienda)->toBe((int) $tiendaA->id);
});


test('los usuarios de una tienda inactiva no pueden acceder a modulos administrativos', function () {
    $tienda = Tienda::factory()->create(['estado' => false]);
    $admin = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
        'id_tienda' => $tienda->id,
    ]);
    $admin->assignRole('administrador');

    $this->actingAs($admin)->get(route('dashboard.index'))->assertForbidden();
    $this->actingAs($admin)->get(route('productos.index'))->assertForbidden();
});


test('un administrador solo puede actualizar el perfil de su propia tienda', function () {
    $tiendaA = Tienda::factory()->create(['nombre' => 'Tienda A']);
    $tiendaB = Tienda::factory()->create(['nombre' => 'Tienda B']);
    $adminA = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
        'id_tienda' => $tiendaA->id,
    ]);
    $adminA->assignRole('administrador');

    $this->actingAs($adminA)->put(route('tiendas.update', $tiendaA->id), [
        'nombre' => 'Tienda A Actualizada',
        'estado' => 1,
    ])->assertRedirect(route('tiendas.index'));

    expect($tiendaA->fresh()->nombre)->toBe('Tienda A Actualizada');

    $this->actingAs($adminA)->put(route('tiendas.update', $tiendaB->id), [
        'nombre' => 'Tienda B Manipulada',
        'estado' => 1,
    ])->assertNotFound();

    expect($tiendaB->fresh()->nombre)->toBe('Tienda B');
});
