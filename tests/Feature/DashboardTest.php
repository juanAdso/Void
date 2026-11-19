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

test('el panel del empleado refleja las facturas del dia sin contar anuladas', function () {
    $tienda = Tienda::factory()->create();
    $empleado = User::factory()->create([
        'estado' => true,
        'rol' => 'empleado',
        'id_tienda' => $tienda->id,
    ]);
    $empleado->assignRole('empleado');
    $cliente = Cliente::factory()->create(['id_tienda' => $tienda->id]);

    foreach ([
        ['estado' => 'pendiente', 'total' => 100],
        ['estado' => 'pagada', 'total' => 200],
        ['estado' => 'anulada', 'total' => 900],
    ] as $datos) {
        Facturas::create([
            'id_cliente' => $cliente->id,
            'id_usuario' => $empleado->id,
            'fecha' => today(),
            'subtotal' => $datos['total'],
            'iva' => 0,
            'total' => $datos['total'],
            'estado' => $datos['estado'],
        ]);
    }

    $response = $this->actingAs($empleado)->get(route('dashboard.index'));

    $response->assertOk()
        ->assertViewHas('ventasHoy', 2)
        ->assertViewHas('ingresosHoy', 200.0)
        ->assertViewHas('facturasRecientes', fn ($facturas) => $facturas->count() === 3)
        ->assertDontSee('Marcar como pagada')
        ->assertDontSee('Anular factura');

    $this->patch(route('facturas.estado', Facturas::query()->firstOrFail()->id), [
        'estado' => 'pagada',
    ])->assertForbidden();
});

test('el empleado regresa al panel actualizado después de registrar una factura', function () {
    $tienda = Tienda::factory()->create();
    $empleado = User::factory()->create([
        'estado' => true,
        'rol' => 'empleado',
        'id_tienda' => $tienda->id,
    ]);
    $empleado->assignRole('empleado');
    $cliente = Cliente::factory()->create(['id_tienda' => $tienda->id]);
    $categoria = Categoria::factory()->create(['id_tienda' => $tienda->id]);
    $producto = Productos::factory()->create([
        'id_tienda' => $tienda->id,
        'id_categoria' => $categoria->id,
        'precio' => 100,
        'stock' => 5,
        'estado' => true,
    ]);

    $response = $this->actingAs($empleado)->post(route('facturas.store'), [
        'id_cliente' => $cliente->id,
        'estado' => 'pendiente',
        'detalles' => [
            ['id_producto' => $producto->id, 'cantidad' => 1, 'precio_unitario' => 100],
        ],
    ]);

    $response->assertRedirect(route('dashboard.index'));
    $this->assertDatabaseHas('facturas', [
        'id_usuario' => $empleado->id,
        'estado' => 'pendiente',
        'total' => 119,
    ]);
    expect($producto->fresh()->stock)->toBe(4);

    $this->get(route('dashboard.index'))
        ->assertOk()
        ->assertViewHas('ventasHoy', 1)
        ->assertViewHas('facturasRecientes', fn ($facturas) => $facturas->count() === 1);
});
