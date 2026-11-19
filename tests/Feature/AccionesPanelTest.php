<?php

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\DetalleFactura;
use App\Models\Facturas;
use App\Models\Productos;
use App\Models\Tienda;
use App\Models\User;
use Database\Seeders\SeguridadSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(SeguridadSeeder::class);

    $this->tienda = Tienda::factory()->create(['estado' => true]);
    $this->admin = User::factory()->create([
        'id_tienda' => $this->tienda->id,
        'rol' => 'administrador',
        'estado' => true,
    ]);
    $this->admin->assignRole('administrador');

    $this->actingAs($this->admin);
});

test('la gestion de categorias permite crear editar y eliminar desde sus formularios', function () {
    $this->get(route('categoria.create'))->assertOk();

    $this->post(route('categoria.store'), [
        'nombre' => 'Categoría de prueba',
        'descripcion' => 'Descripción inicial',
        'estado' => 1,
    ])->assertRedirect(route('categoria.index'));

    $categoria = Categoria::query()->where('id_tienda', $this->tienda->id)->where('nombre', 'Categoría de prueba')->firstOrFail();

    $this->get(route('categoria.edit', $categoria->id))->assertOk()->assertSee('Categoría de prueba');

    $this->put(route('categoria.update', ['categorium' => $categoria->id]), [
        'nombre' => 'Categoría actualizada',
        'descripcion' => 'Descripción modificada',
        'estado' => 1,
    ])->assertRedirect(route('categoria.index'));

    expect($categoria->fresh()->nombre)->toBe('Categoría actualizada');

    $this->delete(route('categoria.destroy', $categoria->id))->assertRedirect(route('categoria.index'));
    $this->assertDatabaseMissing('categorias', ['id' => $categoria->id]);
});

test('la gestion de clientes permite crear editar y eliminar desde sus formularios', function () {
    $this->get(route('cliente.create'))->assertOk();

    $this->post(route('cliente.store'), [
        'nombre' => 'Cliente',
        'apellido' => 'De prueba',
        'documento' => '9001234567',
        'telefono' => '3001234567',
        'correo' => 'cliente.panel@example.com',
        'direccion' => 'Calle 10',
    ])->assertRedirect(route('cliente.index'));

    $cliente = Cliente::query()->where('id_tienda', $this->tienda->id)->where('documento', '9001234567')->firstOrFail();

    $this->get(route('cliente.edit', $cliente->id))->assertOk()->assertSee('cliente.panel@example.com');

    $this->put(route('cliente.update', ['cliente' => $cliente->id]), [
        'nombre' => 'Cliente actualizado',
        'apellido' => 'Prueba',
        'documento' => '9001234567',
        'telefono' => '3119876543',
        'correo' => 'cliente.actualizado@example.com',
        'direccion' => 'Carrera 20',
    ])->assertRedirect(route('cliente.index'));

    expect($cliente->fresh()->correo)->toBe('cliente.actualizado@example.com');

    $this->delete(route('cliente.destroy', $cliente->id))->assertRedirect(route('cliente.index'));
    $this->assertDatabaseMissing('clientes', ['id' => $cliente->id]);
});

test('la gestion de productos permite crear editar y eliminar un producto sin movimientos', function () {
    $categoria = Categoria::factory()->create(['id_tienda' => $this->tienda->id]);

    $this->get(route('productos.create'))->assertOk();

    $this->post(route('productos.store'), [
        'nombre' => 'Producto de prueba',
        'descripcion' => 'Producto para comprobar el formulario',
        'precio' => 25000,
        'stock' => 8,
        'estado' => 1,
        'id_categoria' => $categoria->id,
    ])->assertRedirect(route('productos.index'));

    $producto = Productos::query()->where('id_tienda', $this->tienda->id)->where('nombre', 'Producto de prueba')->firstOrFail();

    $this->get(route('productos.edit', $producto->id))->assertOk()->assertSee('Producto de prueba');

    $this->put(route('productos.update', $producto->id), [
        'nombre' => 'Producto actualizado',
        'descripcion' => 'Descripción actualizada',
        'precio' => 27000,
        'stock' => 8,
        'estado' => 1,
        'id_categoria' => $categoria->id,
    ])->assertRedirect(route('productos.index'));

    expect($producto->fresh()->nombre)->toBe('Producto actualizado');

    $this->delete(route('productos.destroy', $producto->id))->assertRedirect(route('productos.index'));
    $this->assertDatabaseMissing('productos', ['id' => $producto->id]);
});

test('la gestion de facturas permite crear editar y eliminar una factura pendiente vacia', function () {
    $cliente = Cliente::factory()->create(['id_tienda' => $this->tienda->id]);

    $this->get(route('facturas.create'))
        ->assertOk()
        ->assertSeeText('Productos y detalles')
        ->assertSee('detalles[__INDEX__][id_producto]', false)
        ->assertSee('detalles[__INDEX__][cantidad]', false)
        ->assertSee('detalles[__INDEX__][precio_unitario]', false);

    $responseCrear = $this->post(route('facturas.store'), [
        'id_cliente' => $cliente->id,
        'fecha' => '2026-11-17',
        'estado' => 'pendiente',
    ]);

    $factura = Facturas::query()->where('id_usuario', $this->admin->id)->firstOrFail();
    $responseCrear->assertRedirect(route('facturas.index'));

    $this->get(route('facturas.edit', $factura->id))->assertRedirect(route('facturas.show', $factura->id));

    $this->put(route('facturas.update', $factura->id), [
        'id_cliente' => $cliente->id,
        'fecha' => '2026-11-16',
        'estado' => 'pendiente',
    ])->assertRedirect(route('facturas.index'));

    expect($factura->fresh()->fecha->toDateString())->toBe('2026-11-16');

    $this->delete(route('facturas.destroy', $factura->id))->assertRedirect(route('facturas.index'));
    $this->assertDatabaseMissing('facturas', ['id' => $factura->id]);
});

test('los formularios de factura solo muestran estados permitidos al crear editar y proteger facturas pagadas', function () {
    $cliente = Cliente::factory()->create(['id_tienda' => $this->tienda->id]);

    $this->get(route('facturas.create'))
        ->assertOk()
        ->assertSee('name="estado" value="pendiente"', false)
        ->assertDontSee('<option value="pagada"', false)
        ->assertDontSee('<option value="anulada"', false);

    $pagada = Facturas::create([
        'id_cliente' => $cliente->id,
        'id_usuario' => $this->admin->id,
        'fecha' => '2026-11-10',
        'subtotal' => 100,
        'iva' => 19,
        'total' => 119,
        'estado' => 'pagada',
    ]);

    $anulada = Facturas::create([
        'id_cliente' => $cliente->id,
        'id_usuario' => $this->admin->id,
        'fecha' => '2026-11-09',
        'subtotal' => 0,
        'iva' => 0,
        'total' => 0,
        'estado' => 'anulada',
    ]);

    $categoria = Categoria::factory()->create(['id_tienda' => $this->tienda->id]);
    $producto = Productos::factory()->create([
        'id_tienda' => $this->tienda->id,
        'id_categoria' => $categoria->id,
        'nombre' => 'Producto de factura editable',
        'precio' => 100,
        'stock' => 10,
        'estado' => true,
    ]);

    foreach ([$pagada, $anulada] as $facturaConDetalle) {
        DetalleFactura::create([
            'id_factura' => $facturaConDetalle->id,
            'id_producto' => $producto->id,
            'cantidad' => 1,
            'precio_unitario' => 100,
            'subtotal' => 100,
        ]);
    }

    $index = $this->get(route('facturas.index'))->assertOk();
    $index->assertSee(route('facturas.show', $pagada->id));
    $index->assertDontSee(route('facturas.edit', $anulada->id));

    $this->get(route('facturas.show', $pagada->id))
        ->assertOk()
        ->assertSee('name="id_cliente"', false)
        ->assertSee('id="fecha" type="date" name="fecha"', false)
        ->assertSee('<option value="pagada"', false)
        ->assertSee('<option value="anulada"', false)
        ->assertDontSee('<option value="pendiente"', false)
        ->assertSee('name="detalles[0][id_producto]"', false)
        ->assertSee('name="detalles[0][cantidad]"', false)
        ->assertSee('name="detalles[0][precio_unitario]"', false)
        ->assertSeeText('Guardar cambios');

    $this->get(route('facturas.show', $anulada->id))
        ->assertOk()
        ->assertDontSee('name="id_cliente"', false)
        ->assertDontSee('name="detalles[0][id_producto]"', false)
        ->assertDontSee('name="detalles[0][cantidad]"', false)
        ->assertDontSee('name="detalles[0][precio_unitario]"', false)
        ->assertSeeText('Sus datos y detalles son de solo lectura.');
});

test('la consulta de factura integra los detalles y las acciones del administrador', function () {
    $cliente = Cliente::factory()->create(['id_tienda' => $this->tienda->id]);
    $categoria = Categoria::factory()->create(['id_tienda' => $this->tienda->id]);
    $producto = Productos::factory()->create([
        'id_tienda' => $this->tienda->id,
        'id_categoria' => $categoria->id,
        'nombre' => 'Producto visible en factura',
        'precio' => 25000,
        'estado' => true,
        'stock' => 10,
    ]);
    $factura = Facturas::create([
        'id_cliente' => $cliente->id,
        'id_usuario' => $this->admin->id,
        'fecha' => '2026-11-17',
        'subtotal' => 25000,
        'iva' => 4750,
        'total' => 29750,
        'estado' => 'pendiente',
    ]);
    DetalleFactura::create([
        'id_factura' => $factura->id,
        'id_producto' => $producto->id,
        'cantidad' => 1,
        'precio_unitario' => 25000,
        'subtotal' => 25000,
    ]);

    Role::findByName('administrador')->givePermissionTo(['editar detallefacturas', 'eliminar detallefacturas']);
    $this->admin->refresh();
    $this->actingAs($this->admin);

    expect($this->admin->tienePermisoActivo('eliminar detallefacturas'))->toBeTrue();

    $response = $this->get(route('facturas.show', $factura->id))->assertOk();

    expect($response->original->getData()['puedeEliminarDetalle'])->toBeTrue();

    $response
        ->assertSeeText('Productos y detalles de factura')
        ->assertSee('Producto visible en factura', false)
        ->assertSeeText('Guardar')
        ->assertSeeText('Retirar')
        ->assertSeeText('Agregar producto')
        ->assertSee('¿Eliminar la factura #', false)
        ->assertSeeText('29.750,00');

    $this->get('/detalle_facturas')->assertStatus(405);
    $this->get('/detalle_facturas/create')->assertNotFound();
});

test('el empleado consulta facturas y solo ve acciones permitidas por sus permisos', function () {
    $empleado = User::factory()->create([
        'id_tienda' => $this->tienda->id,
        'rol' => 'empleado',
        'estado' => true,
    ]);
    $empleado->assignRole('empleado');

    $cliente = Cliente::factory()->create(['id_tienda' => $this->tienda->id]);
    $categoria = Categoria::factory()->create(['id_tienda' => $this->tienda->id]);
    $producto = Productos::factory()->create([
        'id_tienda' => $this->tienda->id,
        'id_categoria' => $categoria->id,
        'nombre' => 'Producto de empleado',
        'estado' => true,
        'stock' => 10,
    ]);
    $factura = Facturas::create([
        'id_cliente' => $cliente->id,
        'id_usuario' => $this->admin->id,
        'fecha' => '2026-11-17',
        'subtotal' => 100,
        'iva' => 19,
        'total' => 119,
        'estado' => 'pendiente',
    ]);
    DetalleFactura::create([
        'id_factura' => $factura->id,
        'id_producto' => $producto->id,
        'cantidad' => 1,
        'precio_unitario' => 100,
        'subtotal' => 100,
    ]);

    $this->actingAs($empleado)->get(route('facturas.index'))
        ->assertOk()
        ->assertDontSee('Estado')
        ->assertDontSee('Pendiente')
        ->assertDontSee(route('facturas.estado', $factura->id));

    $this->actingAs($empleado)->patch(route('facturas.estado', $factura->id), [
        'estado' => 'anulada',
    ])->assertForbidden();

    $this->actingAs($empleado)->get(route('facturas.show', $factura->id))
        ->assertOk()
        ->assertSee('id="buscar-cliente"', false)
        ->assertDontSee('name="fecha"', false)
        ->assertDontSee('name="estado"', false)
        ->assertSee('Producto de empleado')
        ->assertSee('Agregar producto')
        ->assertDontSee('Editar producto')
        ->assertDontSee('Eliminar producto')
        ->assertDontSee('Eliminar factura');

    $this->actingAs($empleado)->get(route('dashboard.index'))
        ->assertOk()
        ->assertDontSee('Reportes e Inventario')
        ->assertDontSee('Reportes y movimientos de inventario')
        ->assertDontSee('Detalle de Facturas')
        ->assertDontSee('Empleados');

    $this->actingAs($empleado)->get(route('productos.index'))
        ->assertOk()
        ->assertDontSee('Acciones')
        ->assertDontSee('Editar')
        ->assertDontSee('Eliminar');

    $this->actingAs($empleado)->get(route('categoria.index'))
        ->assertOk()
        ->assertDontSee('Acciones')
        ->assertDontSee('Editar')
        ->assertDontSee('Eliminar');

    $this->actingAs($empleado)->get(route('cliente.index'))
        ->assertOk()
        ->assertSee('Acciones')
        ->assertSee('Editar')
        ->assertDontSee('Eliminar');
});

test('el empleado puede crear y editar clientes pero no eliminarlos', function () {
    $empleado = User::factory()->create([
        'id_tienda' => $this->tienda->id,
        'rol' => 'empleado',
        'estado' => true,
    ]);
    $empleado->assignRole('empleado');

    $this->actingAs($empleado)->get(route('cliente.create'))->assertOk();
    $this->actingAs($empleado)->post(route('cliente.store'), [
        'nombre' => 'Cliente empleado',
        'apellido' => 'Prueba',
        'documento' => '9012345678',
        'telefono' => '3001112233',
        'correo' => 'cliente.empleado@example.com',
        'direccion' => 'Calle de prueba',
    ])->assertRedirect(route('cliente.index'));

    $cliente = Cliente::query()
        ->where('id_tienda', $this->tienda->id)
        ->where('documento', '9012345678')
        ->firstOrFail();

    $this->actingAs($empleado)->get(route('cliente.edit', $cliente->id))
        ->assertOk()
        ->assertSee('Cliente empleado');

    $this->actingAs($empleado)->put(route('cliente.update', $cliente->id), [
        'nombre' => 'Cliente actualizado por empleado',
        'apellido' => 'Prueba',
        'documento' => '9012345678',
        'telefono' => '3001112233',
        'correo' => 'cliente.empleado@example.com',
        'direccion' => 'Carrera de prueba',
    ])->assertRedirect(route('cliente.index'));

    $this->actingAs($empleado)->delete(route('cliente.destroy', $cliente->id))
        ->assertForbidden();

    expect($cliente->fresh()->nombre)->toBe('Cliente actualizado por empleado');
});

test('el empleado no puede cambiar la fecha al crear facturas aunque envie otro valor', function () {
    $empleado = User::factory()->create([
        'id_tienda' => $this->tienda->id,
        'rol' => 'empleado',
        'estado' => true,
    ]);
    $empleado->assignRole('empleado');
    $cliente = Cliente::factory()->create(['id_tienda' => $this->tienda->id]);
    $fechaActual = now()->toDateString();

    $this->actingAs($empleado)->get(route('facturas.create'))
        ->assertOk()
        ->assertSee('La fecha de emisión se asigna automáticamente.')
        ->assertSee('disabled', false);

    $this->actingAs($empleado)->post(route('facturas.store'), [
        'id_cliente' => $cliente->id,
        'fecha' => '2000-01-01',
        'estado' => 'pendiente',
    ])->assertRedirect(route('dashboard.index'));

    $factura = Facturas::query()
        ->where('id_usuario', $empleado->id)
        ->latest('id')
        ->firstOrFail();

    expect($factura->fecha->toDateString())->toBe($fechaActual);
});

test('el administrador cambia el estado desde el listado y anular restituye existencias', function () {
    $cliente = Cliente::factory()->create(['id_tienda' => $this->tienda->id]);
    $facturaPagada = Facturas::create([
        'id_cliente' => $cliente->id,
        'id_usuario' => $this->admin->id,
        'fecha' => now()->toDateString(),
        'subtotal' => 0,
        'iva' => 0,
        'total' => 0,
        'estado' => 'pendiente',
    ]);

    $categoria = Categoria::factory()->create(['id_tienda' => $this->tienda->id]);
    $producto = Productos::factory()->create([
        'id_tienda' => $this->tienda->id,
        'id_categoria' => $categoria->id,
        'nombre' => 'Producto para anulación desde listado',
        'stock' => 9,
        'estado' => true,
    ]);
    $facturaAnulada = Facturas::create([
        'id_cliente' => $cliente->id,
        'id_usuario' => $this->admin->id,
        'fecha' => now()->toDateString(),
        'subtotal' => 100,
        'iva' => 19,
        'total' => 119,
        'estado' => 'pendiente',
    ]);
    DetalleFactura::create([
        'id_factura' => $facturaAnulada->id,
        'id_producto' => $producto->id,
        'cantidad' => 1,
        'precio_unitario' => 100,
        'subtotal' => 100,
    ]);

    $this->get(route('facturas.index'))
        ->assertOk()
        ->assertSee('title="Marcar como pagada"', false)
        ->assertSee('title="Anular factura"', false);

    $this->patch(route('facturas.estado', $facturaPagada->id), [
        'estado' => 'pagada',
    ])->assertRedirect(route('facturas.index'));
    expect($facturaPagada->fresh()->estado)->toBe('pagada');

    $this->patch(route('facturas.estado', $facturaAnulada->id), [
        'estado' => 'anulada',
    ])->assertRedirect(route('facturas.index'));
    expect($facturaAnulada->fresh()->estado)->toBe('anulada');
    expect($producto->fresh()->stock)->toBe(10);
});

test('el administrador ve los productos disponibles al editar detalles aunque no pueda crear nuevos', function () {
    $cliente = Cliente::factory()->create(['id_tienda' => $this->tienda->id]);
    $categoria = Categoria::factory()->create(['id_tienda' => $this->tienda->id]);
    $producto = Productos::factory()->create([
        'id_tienda' => $this->tienda->id,
        'id_categoria' => $categoria->id,
        'nombre' => 'Producto editable de factura',
        'estado' => true,
        'stock' => 8,
    ]);
    $factura = Facturas::create([
        'id_cliente' => $cliente->id,
        'id_usuario' => $this->admin->id,
        'fecha' => '2026-11-17',
        'subtotal' => 100,
        'iva' => 19,
        'total' => 119,
        'estado' => 'pendiente',
    ]);
    DetalleFactura::create([
        'id_factura' => $factura->id,
        'id_producto' => $producto->id,
        'cantidad' => 1,
        'precio_unitario' => 100,
        'subtotal' => 100,
    ]);

    Role::findByName('administrador')->revokePermissionTo('crear detallefacturas');

    $this->get(route('facturas.show', $factura->id))
        ->assertOk()
        ->assertSee('name="detalles[0][id_producto]"', false)
        ->assertSee('Producto editable de factura')
        ->assertDontSee('Agregar producto');
});

test('si se bloquea la eliminacion de un producto se muestran las facturas asociadas', function () {
    $categoria = Categoria::factory()->create(['id_tienda' => $this->tienda->id]);
    $cliente = Cliente::factory()->create(['id_tienda' => $this->tienda->id]);
    $producto = Productos::factory()->create([
        'id_tienda' => $this->tienda->id,
        'id_categoria' => $categoria->id,
        'nombre' => 'Producto con historial',
        'estado' => true,
        'stock' => 8,
    ]);
    $factura = Facturas::create([
        'id_cliente' => $cliente->id,
        'id_usuario' => $this->admin->id,
        'fecha' => '2026-11-17',
        'subtotal' => 100,
        'iva' => 19,
        'total' => 119,
        'estado' => 'pendiente',
    ]);
    DetalleFactura::create([
        'id_factura' => $factura->id,
        'id_producto' => $producto->id,
        'cantidad' => 1,
        'precio_unitario' => 100,
        'subtotal' => 100,
    ]);

    $this->delete(route('productos.destroy', $producto->id))
        ->assertRedirect(route('productos.index'));

    $this->get(route('productos.index'))
        ->assertOk()
        ->assertSee('No se puede eliminar este producto')
        ->assertSee('Facturas asociadas')
        ->assertSee('Factura #'.$factura->id)
        ->assertSee('Editar detalle')
        ->assertSee(route('facturas.show', $factura->id));

    $this->assertDatabaseHas('productos', ['id' => $producto->id]);
});

test('los paneles no muestran accesos cuando el permiso correspondiente no existe', function () {
    Role::findByName('administrador')->revokePermissionTo('gestionar seguridad');

    $this->get(route('dashboard.index'))
        ->assertOk()
        ->assertDontSee('Reportes y movimientos de inventario')
        ->assertDontSee('Abrir reportes');
});

test('la edicion de factura integra los campos de sus detalles en la misma vista', function () {
    $cliente = Cliente::factory()->create(['id_tienda' => $this->tienda->id]);
    $categoria = Categoria::factory()->create(['id_tienda' => $this->tienda->id]);
    $producto = Productos::factory()->create([
        'id_tienda' => $this->tienda->id,
        'id_categoria' => $categoria->id,
        'nombre' => 'Producto editable desde factura',
        'precio' => 25000,
        'estado' => true,
        'stock' => 8,
    ]);
    $factura = Facturas::create([
        'id_cliente' => $cliente->id,
        'id_usuario' => $this->admin->id,
        'fecha' => '2026-11-17',
        'subtotal' => 50000,
        'iva' => 9500,
        'total' => 59500,
        'estado' => 'pendiente',
    ]);
    DetalleFactura::create([
        'id_factura' => $factura->id,
        'id_producto' => $producto->id,
        'cantidad' => 2,
        'precio_unitario' => 25000,
        'subtotal' => 50000,
    ]);

    Role::findByName('administrador')->givePermissionTo([
        'crear detallefacturas',
        'editar detallefacturas',
        'eliminar detallefacturas',
    ]);
    $this->admin->refresh();

    $response = $this->get(route('facturas.show', $factura->id))->assertOk();

    $response
        ->assertSeeText('Datos generales de la factura')
        ->assertSee('name="id_cliente"', false)
        ->assertSee('name="fecha"', false)
        ->assertSeeText('Productos y detalles de factura')
        ->assertSee('Producto editable desde factura', false)
        ->assertSee('name="detalles[0][id_producto]"', false)
        ->assertSee('name="detalles[0][cantidad]"', false)
        ->assertSee('name="detalles[0][precio_unitario]"', false)
        ->assertSeeText('Agregar producto')
        ->assertSeeText('Guardar cambios');
});
