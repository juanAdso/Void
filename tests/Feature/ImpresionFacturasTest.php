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

    $this->empleado = User::factory()->create([
        'id_tienda' => $this->tienda->id,
        'rol' => 'empleado',
        'estado' => true,
    ]);
    $this->empleado->assignRole('empleado');

    $this->cliente = Cliente::factory()->create(['id_tienda' => $this->tienda->id]);
    $this->categoria = Categoria::factory()->create(['id_tienda' => $this->tienda->id]);
    $this->producto = Productos::factory()->create([
        'id_tienda' => $this->tienda->id,
        'id_categoria' => $this->categoria->id,
        'nombre' => 'Producto para impresión',
        'precio' => 150,
        'stock' => 4,
        'estado' => true,
    ]);

    $this->factura = Facturas::create([
        'id_cliente' => $this->cliente->id,
        'id_usuario' => $this->admin->id,
        'fecha' => today(),
        'subtotal' => 150,
        'iva' => 28.5,
        'total' => 178.5,
        'estado' => 'pendiente',
    ]);

    DetalleFactura::create([
        'id_factura' => $this->factura->id,
        'id_producto' => $this->producto->id,
        'cantidad' => 1,
        'precio_unitario' => 150,
        'subtotal' => 150,
    ]);
});

test('administrador y empleado pueden imprimir facturas de su tienda', function () {
    foreach ([$this->admin, $this->empleado] as $usuario) {
        $response = $this->actingAs($usuario)->get(route('facturas.imprimir', $this->factura->id));

        $response->assertOk();
        expect($response->headers->get('content-type'))->toContain('application/pdf');
        expect(substr($response->getContent(), 0, 4))->toBe('%PDF');
    }
});

test('la vista de factura ofrece la opción de impresión a los usuarios autorizados', function () {
    $this->actingAs($this->empleado)
        ->get(route('facturas.show', $this->factura->id))
        ->assertOk()
        ->assertSee('Imprimir factura')
        ->assertSee(route('facturas.imprimir', $this->factura->id));
});

test('una tienda no puede imprimir facturas pertenecientes a otra tienda', function () {
    $otraTienda = Tienda::factory()->create(['estado' => true]);
    $otroAdmin = User::factory()->create([
        'id_tienda' => $otraTienda->id,
        'rol' => 'administrador',
        'estado' => true,
    ]);
    $otroAdmin->assignRole('administrador');
    $otroCliente = Cliente::factory()->create(['id_tienda' => $otraTienda->id]);
    $facturaAjena = Facturas::create([
        'id_cliente' => $otroCliente->id,
        'id_usuario' => $otroAdmin->id,
        'fecha' => today(),
        'subtotal' => 0,
        'iva' => 0,
        'total' => 0,
        'estado' => 'pendiente',
    ]);

    $this->actingAs($this->admin)
        ->get(route('facturas.imprimir', $facturaAjena->id))
        ->assertNotFound();
});

test('el listado muestra las acciones para consultar e imprimir la factura', function () {
    $this->actingAs($this->admin)
        ->get(route('facturas.index'))
        ->assertOk()
        ->assertSee('Ver factura')
        ->assertSee(route('facturas.imprimir', $this->factura->id));
});
