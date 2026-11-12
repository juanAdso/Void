<?php

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\DetalleFactura;
use App\Models\Facturas;
use App\Models\MovimientoInventario;
use App\Models\Productos;
use App\Models\Tienda;
use App\Models\User;
use App\Repositories\DetalleFacturaRepository;
use App\Services\DetalleFacturaService;
use Database\Seeders\SeguridadSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(SeguridadSeeder::class);

    $this->tienda = Tienda::create([
        'nombre' => 'Tienda Central',
        'direccion' => 'Calle 10 # 20 - 30',
        'telefono' => '3001234567',
        'correo' => 'tienda@example.com',
        'estado' => true,
    ]);

    $this->usuario = User::create([
        'id_tienda' => $this->tienda->id,
        'nombre' => 'Admin',
        'apellido' => 'Ventas',
        'documento' => '1001234567',
        'correo' => 'adminventas@example.com',
        'password' => bcrypt('password123'),
        'rol' => 'administrador',
        'estado' => true,
    ]);
    $this->usuario->assignRole('administrador');

    $this->categoria = Categoria::create([
        'id_tienda' => $this->tienda->id,
        'nombre' => 'Tecnología',
        'descripcion' => 'Electrónica y cómputo',
        'estado' => true,
    ]);

    $this->cliente = Cliente::create([
        'id_tienda' => $this->tienda->id,
        'nombre' => 'Carlos',
        'apellido' => 'Gomez',
        'documento' => '1122334455',
        'correo' => 'carlos@example.com',
        'telefono' => '3119876543',
        'direccion' => 'Carrera 15 # 45 - 60',
    ]);
});

test('crear factura en estado pendiente con totales iniciales en cero', function () {
    $response = $this->actingAs($this->usuario)->post(route('facturas.store'), [
        'id_cliente' => $this->cliente->id,
        'fecha' => '2026-11-11',
        'estado' => 'pendiente',
        'subtotal' => 9999, // intento de manipular
        'iva' => 9999,
        'total' => 9999,
    ]);

    $response->assertRedirect(route('facturas.index'));

    $factura = Facturas::first();
    expect($factura)->not->toBeNull();
    expect($factura->estado)->toBe('pendiente');
    expect((float) $factura->subtotal)->toBe(0.0);
    expect((float) $factura->iva)->toBe(0.0);
    expect((float) $factura->total)->toBe(0.0);
    expect($factura->id_usuario)->toBe($this->usuario->id);
});

test('agregar detalle descuenta inventario, registra movimiento y recalcula totales con IVA del 19%', function () {
    $producto = Productos::create([
        'id_categoria' => $this->categoria->id,
        'id_tienda' => $this->tienda->id,
        'nombre' => 'Laptop Gamer',
        'descripcion' => 'Core i7',
        'precio' => 1000.00,
        'stock' => 10,
        'estado' => true,
    ]);

    $factura = Facturas::create([
        'id_cliente' => $this->cliente->id,
        'id_usuario' => $this->usuario->id,
        'fecha' => '2026-11-11',
        'subtotal' => 0,
        'iva' => 0,
        'total' => 0,
        'estado' => 'pendiente',
    ]);

    $response = $this->actingAs($this->usuario)->post(route('detalle_facturas.store'), [
        'id_factura' => $factura->id,
        'id_producto' => $producto->id,
        'cantidad' => 3,
        'precio_unitario' => 100.00,
    ]);

    $response->assertRedirect(route('detalle_facturas.index'));

    expect($producto->fresh()->stock)->toBe(7);

    $facturaActualizada = $factura->fresh();
    expect((float) $facturaActualizada->subtotal)->toBe(300.00);
    expect((float) $facturaActualizada->iva)->toBe(57.00); // 300 * 0.19 = 57
    expect((float) $facturaActualizada->total)->toBe(357.00);

    // Auditoría
    $movimiento = MovimientoInventario::where('id_producto', $producto->id)->latest()->first();
    expect($movimiento)->not->toBeNull();
    expect($movimiento->tipo_movimiento)->toBe('venta_detalle');
    expect($movimiento->cantidad)->toBe(-3);
    expect($movimiento->id_tienda)->toBe($this->tienda->id);
});

test('rechazar venta cuando el stock es insuficiente', function () {
    $producto = Productos::create([
        'id_categoria' => $this->categoria->id,
        'id_tienda' => $this->tienda->id,
        'nombre' => 'Monitor 4K',
        'descripcion' => 'Monitor 27',
        'precio' => 500.00,
        'stock' => 2,
        'estado' => true,
    ]);

    $factura = Facturas::create([
        'id_cliente' => $this->cliente->id,
        'id_usuario' => $this->usuario->id,
        'fecha' => '2026-11-11',
        'subtotal' => 0,
        'iva' => 0,
        'total' => 0,
        'estado' => 'pendiente',
    ]);

    $response = $this->actingAs($this->usuario)->post(route('detalle_facturas.store'), [
        'id_factura' => $factura->id,
        'id_producto' => $producto->id,
        'cantidad' => 5,
        'precio_unitario' => 500.00,
    ]);

    $response->assertSessionHasErrors(['cantidad']);
    expect($producto->fresh()->stock)->toBe(2);
    expect(DetalleFactura::count())->toBe(0);
});

test('modificar cantidad de detalle actualiza correctamente el stock y los totales', function () {
    $producto = Productos::create([
        'id_categoria' => $this->categoria->id,
        'id_tienda' => $this->tienda->id,
        'nombre' => 'Teclado Mecanico',
        'descripcion' => 'RGB Switch Red',
        'precio' => 100.00,
        'stock' => 10,
        'estado' => true,
    ]);

    $factura = Facturas::create([
        'id_cliente' => $this->cliente->id,
        'id_usuario' => $this->usuario->id,
        'fecha' => '2026-11-11',
        'subtotal' => 0,
        'iva' => 0,
        'total' => 0,
        'estado' => 'pendiente',
    ]);

    $this->actingAs($this->usuario)->post(route('detalle_facturas.store'), [
        'id_factura' => $factura->id,
        'id_producto' => $producto->id,
        'cantidad' => 2,
        'precio_unitario' => 100.00,
    ]);

    expect($producto->fresh()->stock)->toBe(8);

    $detalle = DetalleFactura::first();

    // Aumentar cantidad de 2 a 5 (requiere 3 más del stock)
    $this->actingAs($this->usuario)->put(route('detalle_facturas.update', $detalle->id), [
        'id_factura' => $factura->id,
        'id_producto' => $producto->id,
        'cantidad' => 5,
        'precio_unitario' => 100.00,
    ]);

    expect($producto->fresh()->stock)->toBe(5);
    expect((float) $factura->fresh()->subtotal)->toBe(500.00);
    expect((float) $factura->fresh()->iva)->toBe(95.00);
    expect((float) $factura->fresh()->total)->toBe(595.00);

    // Disminuir cantidad de 5 a 1 (restituye 4 al stock)
    $this->actingAs($this->usuario)->put(route('detalle_facturas.update', $detalle->id), [
        'id_factura' => $factura->id,
        'id_producto' => $producto->id,
        'cantidad' => 1,
        'precio_unitario' => 100.00,
    ]);

    expect($producto->fresh()->stock)->toBe(9);
    expect((float) $factura->fresh()->subtotal)->toBe(100.00);
    expect((float) $factura->fresh()->iva)->toBe(19.00);
    expect((float) $factura->fresh()->total)->toBe(119.00);
});

test('cambiar producto restituye inventario del anterior y descuenta del nuevo', function () {
    $productoA = Productos::create([
        'id_categoria' => $this->categoria->id,
        'id_tienda' => $this->tienda->id,
        'nombre' => 'Producto A',
        'descripcion' => 'Desc A',
        'precio' => 50.00,
        'stock' => 10,
        'estado' => true,
    ]);

    $productoB = Productos::create([
        'id_categoria' => $this->categoria->id,
        'id_tienda' => $this->tienda->id,
        'nombre' => 'Producto B',
        'descripcion' => 'Desc B',
        'precio' => 80.00,
        'stock' => 10,
        'estado' => true,
    ]);

    $factura = Facturas::create([
        'id_cliente' => $this->cliente->id,
        'id_usuario' => $this->usuario->id,
        'fecha' => '2026-11-11',
        'subtotal' => 0,
        'iva' => 0,
        'total' => 0,
        'estado' => 'pendiente',
    ]);

    $this->actingAs($this->usuario)->post(route('detalle_facturas.store'), [
        'id_factura' => $factura->id,
        'id_producto' => $productoA->id,
        'cantidad' => 4,
        'precio_unitario' => 50.00,
    ]);

    expect($productoA->fresh()->stock)->toBe(6);
    expect($productoB->fresh()->stock)->toBe(10);

    $detalle = DetalleFactura::first();

    // Cambiar a producto B con cantidad 3
    $this->actingAs($this->usuario)->put(route('detalle_facturas.update', $detalle->id), [
        'id_factura' => $factura->id,
        'id_producto' => $productoB->id,
        'cantidad' => 3,
        'precio_unitario' => 80.00,
    ]);

    expect($productoA->fresh()->stock)->toBe(10); // restituido
    expect($productoB->fresh()->stock)->toBe(7);  // descontado
    expect((float) $factura->fresh()->subtotal)->toBe(240.00);
    expect((float) $factura->fresh()->iva)->toBe(45.60);
    expect((float) $factura->fresh()->total)->toBe(285.60);

    // Verificar movimientos registrados
    expect(MovimientoInventario::where('id_producto', $productoA->id)->where('tipo_movimiento', 'cambio_producto_restitucion')->count())->toBe(1);
    expect(MovimientoInventario::where('id_producto', $productoB->id)->where('tipo_movimiento', 'cambio_producto_salida')->count())->toBe(1);
});

test('eliminar detalle de factura pendiente restituye el inventario y recalcula factura', function () {
    $producto = Productos::create([
        'id_categoria' => $this->categoria->id,
        'id_tienda' => $this->tienda->id,
        'nombre' => 'Mouse USB',
        'descripcion' => 'Mouse optico',
        'precio' => 20.00,
        'stock' => 15,
        'estado' => true,
    ]);

    $factura = Facturas::create([
        'id_cliente' => $this->cliente->id,
        'id_usuario' => $this->usuario->id,
        'fecha' => '2026-11-11',
        'subtotal' => 0,
        'iva' => 0,
        'total' => 0,
        'estado' => 'pendiente',
    ]);

    $this->actingAs($this->usuario)->post(route('detalle_facturas.store'), [
        'id_factura' => $factura->id,
        'id_producto' => $producto->id,
        'cantidad' => 5,
        'precio_unitario' => 20.00,
    ]);

    expect($producto->fresh()->stock)->toBe(10);

    $detalle = DetalleFactura::first();

    $this->actingAs($this->usuario)->delete(route('detalle_facturas.destroy', $detalle->id));

    expect($producto->fresh()->stock)->toBe(15);
    expect((float) $factura->fresh()->subtotal)->toBe(0.00);
    expect((float) $factura->fresh()->total)->toBe(0.00);
    expect(DetalleFactura::count())->toBe(0);

    $movimiento = MovimientoInventario::where('id_producto', $producto->id)->latest('id')->first();
    expect($movimiento->tipo_movimiento)->toBe('devolucion_detalle');
    expect($movimiento->cantidad)->toBe(5);
});

test('anular factura restituye inventario de todos sus detalles', function () {
    $prod1 = Productos::create([
        'id_categoria' => $this->categoria->id,
        'id_tienda' => $this->tienda->id,
        'nombre' => 'Prod 1',
        'descripcion' => 'Desc 1',
        'precio' => 10.00,
        'stock' => 20,
        'estado' => true,
    ]);

    $prod2 = Productos::create([
        'id_categoria' => $this->categoria->id,
        'id_tienda' => $this->tienda->id,
        'nombre' => 'Prod 2',
        'descripcion' => 'Desc 2',
        'precio' => 30.00,
        'stock' => 20,
        'estado' => true,
    ]);

    $factura = Facturas::create([
        'id_cliente' => $this->cliente->id,
        'id_usuario' => $this->usuario->id,
        'fecha' => '2026-11-11',
        'subtotal' => 0,
        'iva' => 0,
        'total' => 0,
        'estado' => 'pendiente',
    ]);

    $this->actingAs($this->usuario)->post(route('detalle_facturas.store'), [
        'id_factura' => $factura->id,
        'id_producto' => $prod1->id,
        'cantidad' => 4,
        'precio_unitario' => 10.00,
    ]);

    $this->actingAs($this->usuario)->post(route('detalle_facturas.store'), [
        'id_factura' => $factura->id,
        'id_producto' => $prod2->id,
        'cantidad' => 6,
        'precio_unitario' => 30.00,
    ]);

    expect($prod1->fresh()->stock)->toBe(16);
    expect($prod2->fresh()->stock)->toBe(14);

    // Anular factura
    $this->actingAs($this->usuario)->put(route('facturas.update', $factura->id), [
        'id_cliente' => $this->cliente->id,
        'fecha' => '2026-11-11',
        'estado' => 'anulada',
    ]);

    expect($factura->fresh()->estado)->toBe('anulada');
    expect($prod1->fresh()->stock)->toBe(20);
    expect($prod2->fresh()->stock)->toBe(20);

    expect(MovimientoInventario::where('id_producto', $prod1->id)->where('tipo_movimiento', 'anulacion_factura')->count())->toBe(1);
    expect(MovimientoInventario::where('id_producto', $prod2->id)->where('tipo_movimiento', 'anulacion_factura')->count())->toBe(1);
});

test('no se permite modificar ni agregar detalles a una factura pagada', function () {
    $producto = Productos::create([
        'id_categoria' => $this->categoria->id,
        'id_tienda' => $this->tienda->id,
        'nombre' => 'Audifonos',
        'descripcion' => 'Bluetooth',
        'precio' => 50.00,
        'stock' => 10,
        'estado' => true,
    ]);

    $factura = Facturas::create([
        'id_cliente' => $this->cliente->id,
        'id_usuario' => $this->usuario->id,
        'fecha' => '2026-11-11',
        'subtotal' => 0,
        'iva' => 0,
        'total' => 0,
        'estado' => 'pendiente',
    ]);

    $this->actingAs($this->usuario)->post(route('detalle_facturas.store'), [
        'id_factura' => $factura->id,
        'id_producto' => $producto->id,
        'cantidad' => 2,
        'precio_unitario' => 50.00,
    ]);

    // Pasar a pagada
    $this->actingAs($this->usuario)->put(route('facturas.update', $factura->id), [
        'id_cliente' => $this->cliente->id,
        'fecha' => '2026-11-11',
        'estado' => 'pagada',
    ]);

    expect($factura->fresh()->estado)->toBe('pagada');

    $detalle = DetalleFactura::first();

    // Intentar agregar nuevo detalle a factura pagada
    $responseAdd = $this->actingAs($this->usuario)->post(route('detalle_facturas.store'), [
        'id_factura' => $factura->id,
        'id_producto' => $producto->id,
        'cantidad' => 1,
        'precio_unitario' => 50.00,
    ]);
    $responseAdd->assertSessionHasErrors(['id_factura']);

    // Intentar modificar detalle existente de factura pagada
    $responseEdit = $this->actingAs($this->usuario)->put(route('detalle_facturas.update', $detalle->id), [
        'id_factura' => $factura->id,
        'id_producto' => $producto->id,
        'cantidad' => 3,
        'precio_unitario' => 50.00,
    ]);
    $responseEdit->assertSessionHasErrors(['id_factura']);

    // Intentar eliminar detalle directamente de factura pagada
    try {
        app(DetalleFacturaService::class)->eliminar($detalle->id, $this->tienda->id);
        $this->fail('Debería arrojar excepción al eliminar detalle de factura pagada.');
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('id_factura');
    }

    expect($detalle->fresh())->not->toBeNull();
    expect($producto->fresh()->stock)->toBe(8);
});

test('no se permite modificar ni alterar una factura anulada', function () {
    $factura = Facturas::create([
        'id_cliente' => $this->cliente->id,
        'id_usuario' => $this->usuario->id,
        'fecha' => '2026-11-11',
        'subtotal' => 0,
        'iva' => 0,
        'total' => 0,
        'estado' => 'anulada',
    ]);

    $response = $this->actingAs($this->usuario)->put(route('facturas.update', $factura->id), [
        'id_cliente' => $this->cliente->id,
        'fecha' => '2026-11-11',
        'estado' => 'pendiente',
    ]);

    $response->assertSessionHasErrors(['estado']);
    expect($factura->fresh()->estado)->toBe('anulada');
});

test('las transacciones revierten todas las operaciones en caso de fallo', function () {
    $producto = Productos::create([
        'id_categoria' => $this->categoria->id,
        'id_tienda' => $this->tienda->id,
        'nombre' => 'Tablet',
        'descripcion' => '10 pulgadas',
        'precio' => 200.00,
        'stock' => 5,
        'estado' => true,
    ]);

    $factura = Facturas::create([
        'id_cliente' => $this->cliente->id,
        'id_usuario' => $this->usuario->id,
        'fecha' => '2026-11-11',
        'subtotal' => 0,
        'iva' => 0,
        'total' => 0,
        'estado' => 'pendiente',
    ]);

    // Si ocurre un error al intentar agregar por stock insuficiente
    try {
        app(DetalleFacturaRepository::class)->guardar([
            'id_factura' => $factura->id,
            'id_producto' => $producto->id,
            'cantidad' => 10, // mayor que 5
            'precio_unitario' => 200.00,
        ], $this->tienda->id);
        $this->fail('Debería fallar por stock insuficiente');
    } catch (ValidationException $e) {
        // Excepción esperada
    }

    // Comprobar que no se creó ningún detalle ni se alteró el stock ni los totales
    expect($producto->fresh()->stock)->toBe(5);
    expect(DetalleFactura::count())->toBe(0);
    expect((float) $factura->fresh()->subtotal)->toBe(0.00);
    expect(MovimientoInventario::count())->toBe(0);
});

test('mover detalle entre dos facturas pendientes recalcula ambas facturas correctamente', function () {
    $producto = Productos::create([
        'id_categoria' => $this->categoria->id,
        'id_tienda' => $this->tienda->id,
        'nombre' => 'Cargador Rapido',
        'descripcion' => 'USB-C',
        'precio' => 50.00,
        'stock' => 10,
        'estado' => true,
    ]);

    $factura1 = Facturas::create([
        'id_cliente' => $this->cliente->id,
        'id_usuario' => $this->usuario->id,
        'fecha' => '2026-11-11',
        'subtotal' => 0,
        'iva' => 0,
        'total' => 0,
        'estado' => 'pendiente',
    ]);

    $factura2 = Facturas::create([
        'id_cliente' => $this->cliente->id,
        'id_usuario' => $this->usuario->id,
        'fecha' => '2026-11-11',
        'subtotal' => 0,
        'iva' => 0,
        'total' => 0,
        'estado' => 'pendiente',
    ]);

    $this->actingAs($this->usuario)->post(route('detalle_facturas.store'), [
        'id_factura' => $factura1->id,
        'id_producto' => $producto->id,
        'cantidad' => 2,
        'precio_unitario' => 50.00,
    ]);

    expect((float) $factura1->fresh()->subtotal)->toBe(100.00);
    expect((float) $factura2->fresh()->subtotal)->toBe(0.00);

    $detalle = DetalleFactura::first();

    // Mover detalle a factura 2
    $this->actingAs($this->usuario)->put(route('detalle_facturas.update', $detalle->id), [
        'id_factura' => $factura2->id,
        'id_producto' => $producto->id,
        'cantidad' => 2,
        'precio_unitario' => 50.00,
    ]);

    expect((float) $factura1->fresh()->subtotal)->toBe(0.00);
    expect((float) $factura1->fresh()->total)->toBe(0.00);

    expect((float) $factura2->fresh()->subtotal)->toBe(100.00);
    expect((float) $factura2->fresh()->iva)->toBe(19.00);
    expect((float) $factura2->fresh()->total)->toBe(119.00);
});
