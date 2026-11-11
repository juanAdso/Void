<?php

use App\Models\Categoria;
use App\Models\Productos;
use App\Models\Tienda;
use Database\Seeders\SeguridadSeeder;

beforeEach(function () {
    $this->seed(SeguridadSeeder::class);
});

test('un visitante puede ver el directorio publico de tiendas', function () {
    $tienda1 = Tienda::factory()->create(['nombre' => 'Tienda Alfa', 'estado' => true]);
    $tienda2 = Tienda::factory()->create(['nombre' => 'Tienda Beta', 'estado' => true]);
    $tiendaInactiva = Tienda::factory()->create(['nombre' => 'Tienda Oculta', 'estado' => false]);

    $response = $this->get(route('inicio'));

    $response->assertOk();
    $response->assertSee('Tienda Alfa');
    $response->assertSee('Tienda Beta');
    $response->assertDontSee('Tienda Oculta');
});

test('un visitante puede consultar la ficha de una tienda activa con datos de contacto', function () {
    $tienda = Tienda::factory()->create([
        'nombre' => 'Boutique Central',
        'telefono' => '3001234567',
        'correo' => 'contacto@boutiquecentral.com',
        'direccion' => 'Calle 100 # 15-20',
        'estado' => true,
    ]);

    $response = $this->get(route('portal.tienda', $tienda->id));

    $response->assertOk();
    $response->assertSee('Boutique Central');
    $response->assertSee('3001234567');
    $response->assertSee('contacto@boutiquecentral.com');
    $response->assertSee('Calle 100 # 15-20');
});

test('intentar ver una tienda inactiva o inexistente retorna 404', function () {
    $tiendaInactiva = Tienda::factory()->create(['estado' => false]);

    $this->get(route('portal.tienda', $tiendaInactiva->id))->assertNotFound();
    $this->get(route('portal.tienda', 999999))->assertNotFound();
});

test('el catalogo por tienda muestra exclusivamente los productos de la tienda elegida', function () {
    $tiendaA = Tienda::factory()->create(['nombre' => 'Tienda A', 'estado' => true]);
    $tiendaB = Tienda::factory()->create(['nombre' => 'Tienda B', 'estado' => true]);
    $categoria = Categoria::factory()->create(['estado' => true]);

    $prodA = Productos::factory()->create([
        'id_tienda' => $tiendaA->id,
        'id_categoria' => $categoria->id,
        'nombre' => 'Laptop Gamer Pro',
        'precio' => 1500,
        'stock' => 10,
        'estado' => true,
    ]);

    $prodB = Productos::factory()->create([
        'id_tienda' => $tiendaB->id,
        'id_categoria' => $categoria->id,
        'nombre' => 'Camisa Elegante',
        'precio' => 50,
        'stock' => 20,
        'estado' => true,
    ]);

    // Visitar catalogo de Tienda A
    $responseA = $this->get(route('catalogo', ['tienda' => $tiendaA->id]));
    $responseA->assertOk();
    $responseA->assertSee('Laptop Gamer Pro');
    $responseA->assertDontSee('Camisa Elegante');

    // Visitar catalogo de Tienda B
    $responseB = $this->get(route('catalogo', ['tienda' => $tiendaB->id]));
    $responseB->assertOk();
    $responseB->assertSee('Camisa Elegante');
    $responseB->assertDontSee('Laptop Gamer Pro');
});

test('parametro de tienda invalido o vacio en catalogo no muestra catalogo global', function () {
    // Si se pasa tienda inexistente
    $this->get(route('catalogo', ['tienda' => 999999]))->assertNotFound();

    // Si se pasa tienda inactiva
    $tiendaInactiva = Tienda::factory()->create(['estado' => false]);
    $this->get(route('catalogo', ['tienda' => $tiendaInactiva->id]))->assertNotFound();
});

test('filtrado por busqueda, categoria, disponibilidad y precio en catalogo', function () {
    $tienda = Tienda::factory()->create(['estado' => true]);
    $cat1 = Categoria::factory()->create(['nombre' => 'Tecnología', 'estado' => true]);
    $cat2 = Categoria::factory()->create(['nombre' => 'Ropa', 'estado' => true]);

    $p1 = Productos::factory()->create([
        'id_tienda' => $tienda->id,
        'id_categoria' => $cat1->id,
        'nombre' => 'Mouse Ergonómico',
        'precio' => 80,
        'stock' => 5,
        'estado' => true,
    ]);

    $p2 = Productos::factory()->create([
        'id_tienda' => $tienda->id,
        'id_categoria' => $cat1->id,
        'nombre' => 'Teclado Mecánico',
        'precio' => 150,
        'stock' => 0, // Agotado
        'estado' => true,
    ]);

    $p3 = Productos::factory()->create([
        'id_tienda' => $tienda->id,
        'id_categoria' => $cat2->id,
        'nombre' => 'Chaqueta Cuero',
        'precio' => 250,
        'stock' => 3,
        'estado' => true,
    ]);

    // 1. Búsqueda por texto
    $resBuscar = $this->get(route('catalogo', ['tienda' => $tienda->id, 'buscar' => 'Mouse']));
    $resBuscar->assertSee('Mouse Ergonómico');
    $resBuscar->assertDontSee('Chaqueta Cuero');

    // 2. Filtro por categoría
    $resCat = $this->get(route('catalogo', ['tienda' => $tienda->id, 'categoria' => $cat2->id]));
    $resCat->assertSee('Chaqueta Cuero');
    $resCat->assertDontSee('Mouse Ergonómico');

    // 3. Filtro por disponibilidad agotados
    $resAgotados = $this->get(route('catalogo', ['tienda' => $tienda->id, 'disponibilidad' => 'agotados']));
    $resAgotados->assertSee('Teclado Mecánico');
    $resAgotados->assertDontSee('Mouse Ergonómico');

    // 4. Filtro por rango de precio
    $resPrecio = $this->get(route('catalogo', ['tienda' => $tienda->id, 'precio_min' => 200]));
    $resPrecio->assertSee('Chaqueta Cuero');
    $resPrecio->assertDontSee('Mouse Ergonómico');
});

test('un visitante puede ver la ficha individual de un producto con contacto de su tienda sin carrito', function () {
    $tienda = Tienda::factory()->create([
        'nombre' => 'Deportes Extremos',
        'telefono' => '3119998877',
        'estado' => true,
    ]);
    $cat = Categoria::factory()->create(['estado' => true]);

    $prod = Productos::factory()->create([
        'id_tienda' => $tienda->id,
        'id_categoria' => $cat->id,
        'nombre' => 'Bicicleta de Montaña',
        'descripcion' => 'Bicicleta con marco de carbono aro 29',
        'precio' => 3200,
        'stock' => 4,
        'estado' => true,
    ]);

    $response = $this->get(route('portal.producto', $prod->id));

    $response->assertOk();
    $response->assertSee('Bicicleta de Montaña');
    $response->assertSee('Bicicleta con marco de carbono aro 29');
    $response->assertSee('Deportes Extremos');
    $response->assertSee('3119998877');
    $response->assertDontSee('Añadir al carrito');
    $response->assertDontSee('Checkout');
});

test('producto de tienda inactiva o producto inactivo retorna 404', function () {
    $tiendaInactiva = Tienda::factory()->create(['estado' => false]);
    $cat = Categoria::factory()->create(['estado' => true]);

    $prod = Productos::factory()->create([
        'id_tienda' => $tiendaInactiva->id,
        'id_categoria' => $cat->id,
        'estado' => true,
    ]);

    $this->get(route('portal.producto', $prod->id))->assertNotFound();

    $tiendaActiva = Tienda::factory()->create(['estado' => true]);
    $prodInactivo = Productos::factory()->create([
        'id_tienda' => $tiendaActiva->id,
        'id_categoria' => $cat->id,
        'estado' => false,
    ]);

    $this->get(route('portal.producto', $prodInactivo->id))->assertNotFound();
});
