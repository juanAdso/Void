<?php

use App\Models\Categoria;
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

test('el historial de movimientos filtra el rango completo e incluye las fechas límite', function () {
    $tienda = Tienda::factory()->create(['estado' => true]);
    $admin = User::factory()->create(['id_tienda' => $tienda->id, 'rol' => 'administrador', 'estado' => true]);
    $admin->assignRole('administrador');

    $categoria = Categoria::factory()->create(['id_tienda' => $tienda->id, 'estado' => true]);
    $producto = Productos::factory()->create([
        'id_tienda' => $tienda->id,
        'id_categoria' => $categoria->id,
    ]);

    foreach ([
        ['fecha' => '2026-11-09 10:00:00', 'motivo' => 'Movimiento anterior al rango'],
        ['fecha' => '2026-11-10 08:00:00', 'motivo' => 'Movimiento del primer día'],
        ['fecha' => '2026-11-20 18:00:00', 'motivo' => 'Movimiento del último día'],
        ['fecha' => '2026-11-21 09:00:00', 'motivo' => 'Movimiento posterior al rango'],
    ] as $movimiento) {
        MovimientoInventario::create([
            'id_producto' => $producto->id,
            'id_tienda' => $tienda->id,
            'id_usuario' => $admin->id,
            'tipo_movimiento' => 'ajuste_inventario',
            'cantidad' => 1,
            'fecha' => $movimiento['fecha'],
            'motivo' => $movimiento['motivo'],
        ]);
    }

    $this->actingAs($admin)
        ->get(route('reportes.index', [
            'mov_fecha_inicio' => '2026-11-10',
            'mov_fecha_fin' => '2026-11-20',
        ]))
        ->assertOk()
        ->assertSeeText('Movimiento del primer día')
        ->assertSeeText('Movimiento del último día')
        ->assertDontSeeText('Movimiento anterior al rango')
        ->assertDontSeeText('Movimiento posterior al rango');
});

test('el historial de movimientos rechaza un rango con fecha final anterior a la inicial', function () {
    $tienda = Tienda::factory()->create(['estado' => true]);
    $admin = User::factory()->create(['id_tienda' => $tienda->id, 'rol' => 'administrador', 'estado' => true]);
    $admin->assignRole('administrador');

    $this->actingAs($admin)
        ->get(route('reportes.index', [
            'mov_fecha_inicio' => '2026-11-20',
            'mov_fecha_fin' => '2026-11-10',
        ]))
        ->assertSessionHasErrors('mov_fecha_fin');
});
