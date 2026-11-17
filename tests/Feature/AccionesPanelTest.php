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

    $this->get(route('facturas.create'))->assertOk();

    $this->post(route('facturas.store'), [
        'id_cliente' => $cliente->id,
        'fecha' => '2026-11-17',
        'estado' => 'pendiente',
    ])->assertRedirect(route('facturas.index'));

    $factura = Facturas::query()->where('id_usuario', $this->admin->id)->firstOrFail();

    $this->get(route('facturas.edit', $factura->id))->assertOk();

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
        ->assertSee('<option value="pendiente"', false)
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

    $index = $this->get(route('facturas.index'))->assertOk();
    $index->assertSee(route('facturas.edit', $pagada->id));
    $index->assertDontSee(route('facturas.edit', $anulada->id));

    $this->get(route('facturas.edit', $pagada->id))
        ->assertOk()
        ->assertSee('type="hidden" name="id_cliente"', false)
        ->assertSee('name="fecha" value="2026-11-10"', false)
        ->assertSee('<option value="pagada"', false)
        ->assertSee('<option value="anulada"', false)
        ->assertDontSee('<option value="pendiente"', false);

    $this->get(route('facturas.edit', $anulada->id))->assertNotFound();
});

test('los detalles solo permiten seleccionar facturas pendientes y productos activos', function () {
    $cliente = Cliente::factory()->create(['id_tienda' => $this->tienda->id]);
    $facturaPendiente = Facturas::create([
        'id_cliente' => $cliente->id,
        'id_usuario' => $this->admin->id,
        'fecha' => '2026-11-12',
        'subtotal' => 0,
        'iva' => 0,
        'total' => 0,
        'estado' => 'pendiente',
    ]);
    $facturaPagada = Facturas::create([
        'id_cliente' => $cliente->id,
        'id_usuario' => $this->admin->id,
        'fecha' => '2026-11-11',
        'subtotal' => 100,
        'iva' => 19,
        'total' => 119,
        'estado' => 'pagada',
    ]);
    $facturaAnulada = Facturas::create([
        'id_cliente' => $cliente->id,
        'id_usuario' => $this->admin->id,
        'fecha' => '2026-11-10',
        'subtotal' => 0,
        'iva' => 0,
        'total' => 0,
        'estado' => 'anulada',
    ]);

    $categoria = Categoria::factory()->create(['id_tienda' => $this->tienda->id]);
    $productoActivo = Productos::factory()->create([
        'id_tienda' => $this->tienda->id,
        'id_categoria' => $categoria->id,
        'estado' => true,
        'stock' => 10,
    ]);
    $productoInactivo = Productos::factory()->create([
        'id_tienda' => $this->tienda->id,
        'id_categoria' => $categoria->id,
        'estado' => false,
        'stock' => 10,
    ]);

    $this->get(route('detalle_facturas.create'))
        ->assertOk()
        ->assertSee('value="'.$facturaPendiente->id.'"', false)
        ->assertDontSee('value="'.$facturaPagada->id.'"', false)
        ->assertDontSee('value="'.$facturaAnulada->id.'"', false)
        ->assertSee('value="'.$productoActivo->id.'"', false)
        ->assertDontSee('value="'.$productoInactivo->id.'"', false);

    $detallePendiente = DetalleFactura::create([
        'id_factura' => $facturaPendiente->id,
        'id_producto' => $productoActivo->id,
        'cantidad' => 1,
        'precio_unitario' => 25000,
        'subtotal' => 25000,
    ]);
    $detallePagado = DetalleFactura::create([
        'id_factura' => $facturaPagada->id,
        'id_producto' => $productoActivo->id,
        'cantidad' => 1,
        'precio_unitario' => 25000,
        'subtotal' => 25000,
    ]);

    $listado = $this->get(route('detalle_facturas.index'))->assertOk();
    $listado->assertSee(route('detalle_facturas.edit', $detallePendiente->id));
    $listado->assertDontSee(route('detalle_facturas.edit', $detallePagado->id));
    $listado->assertDontSee(route('detalle_facturas.destroy', $detallePagado->id));

    $this->get(route('detalle_facturas.edit', $detallePendiente->id))->assertOk();
    $this->get(route('detalle_facturas.edit', $detallePagado->id))->assertNotFound();
});
