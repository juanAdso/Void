<?php

use App\Models\Categoria;
use App\Models\Facturas;
use App\Models\MovimientoInventario;
use App\Models\Productos;
use App\Models\Tienda;
use App\Models\User;
use Database\Seeders\SeguridadSeeder;

beforeEach(function () {
    $this->seed(SeguridadSeeder::class);
});

test('el modulo de reportes muestra las alertas de stock bajo y productos agotados de la tienda', function () {
    $tienda = Tienda::factory()->create(['estado' => true]);
    $admin = User::factory()->create(['id_tienda' => $tienda->id, 'rol' => 'administrador', 'estado' => true]);
    $admin->assignRole('administrador');

    $cat = Categoria::factory()->create(['id_tienda' => $tienda->id, 'estado' => true]);

    $prodCritico = Productos::factory()->create([
        'id_tienda' => $tienda->id,
        'id_categoria' => $cat->id,
        'nombre' => 'Producto Por Agotarse',
        'stock' => 2,
        'estado' => true,
    ]);

    $prodAgotado = Productos::factory()->create([
        'id_tienda' => $tienda->id,
        'id_categoria' => $cat->id,
        'nombre' => 'Producto Totalmente Agotado',
        'stock' => 0,
        'estado' => true,
    ]);

    $prodNormal = Productos::factory()->create([
        'id_tienda' => $tienda->id,
        'id_categoria' => $cat->id,
        'nombre' => 'Producto Abundante',
        'stock' => 50,
        'estado' => true,
    ]);

    $response = $this->actingAs($admin)->get(route('reportes.index'));

    $response->assertOk();
    $response->assertSee('Producto Por Agotarse');
    $response->assertSee('Producto Totalmente Agotado');
    $response->assertDontSee('Producto Abundante');
});

test('el modulo de reportes muestra el historial de movimientos de inventario de la tienda', function () {
    $tienda = Tienda::factory()->create(['estado' => true]);
    $admin = User::factory()->create(['id_tienda' => $tienda->id, 'rol' => 'administrador', 'estado' => true]);
    $admin->assignRole('administrador');

    $cat = Categoria::factory()->create(['estado' => true]);
    $prod = Productos::factory()->create(['id_tienda' => $tienda->id, 'id_categoria' => $cat->id, 'nombre' => 'Audifonos Bluetooth']);

    MovimientoInventario::create([
        'id_producto' => $prod->id,
        'id_tienda' => $tienda->id,
        'id_usuario' => $admin->id,
        'tipo_movimiento' => 'venta_detalle',
        'cantidad' => -2,
        'fecha' => now(),
        'motivo' => 'Venta registrada en factura #101',
    ]);

    $response = $this->actingAs($admin)->get(route('reportes.index'));

    $response->assertOk();
    $response->assertSee('Audifonos Bluetooth');
    $response->assertSee('Venta registrada en factura #101');
});
