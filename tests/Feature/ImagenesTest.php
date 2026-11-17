<?php

use App\Models\Categoria;
use App\Models\Productos;
use App\Models\Tienda;
use App\Models\User;
use Database\Seeders\CuentasDemostracionSeeder;
use Database\Seeders\SeguridadSeeder;
use Database\Seeders\TiendaSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(SeguridadSeeder::class);
});

function administradorDeTienda(Tienda $tienda): User
{
    $usuario = User::factory()->create([
        'estado' => true,
        'rol' => 'administrador',
        'id_tienda' => $tienda->id,
    ]);
    $usuario->assignRole('administrador');

    return $usuario;
}

test('un administrador puede cargar una imagen de producto dentro de su propia tienda', function () {
    Storage::fake('public');

    $tienda = Tienda::factory()->create();
    $admin = administradorDeTienda($tienda);
    $categoria = Categoria::factory()->create(['id_tienda' => $tienda->id]);

    $response = $this->actingAs($admin)->post(route('productos.store'), [
        'nombre' => 'Producto con fotografía',
        'descripcion' => 'Producto de prueba',
        'precio' => 50000,
        'stock' => 5,
        'estado' => 1,
        'id_categoria' => $categoria->id,
        'imagen' => UploadedFile::fake()->image('producto.jpg'),
    ]);

    $response->assertRedirect(route('productos.index'));

    $producto = Productos::query()->where('nombre', 'Producto con fotografía')->firstOrFail();

    expect($producto->imagen)->toStartWith("tiendas/{$tienda->id}/productos/");
    Storage::disk('public')->assertExists($producto->imagen);
});

test('reemplazar la imagen de un producto elimina el archivo anterior', function () {
    Storage::fake('public');

    $tienda = Tienda::factory()->create();
    $admin = administradorDeTienda($tienda);
    $categoria = Categoria::factory()->create(['id_tienda' => $tienda->id]);
    $rutaAnterior = "tiendas/{$tienda->id}/productos/anterior.jpg";
    Storage::disk('public')->put($rutaAnterior, 'imagen anterior');

    $producto = Productos::factory()->create([
        'id_tienda' => $tienda->id,
        'id_categoria' => $categoria->id,
        'imagen' => $rutaAnterior,
    ]);

    $response = $this->actingAs($admin)->put(route('productos.update', $producto->id), [
        'nombre' => $producto->nombre,
        'descripcion' => $producto->descripcion,
        'precio' => $producto->precio,
        'stock' => $producto->stock,
        'estado' => 1,
        'id_categoria' => $categoria->id,
        'imagen' => UploadedFile::fake()->image('nueva.jpg'),
    ]);

    $response->assertRedirect(route('productos.index'));

    $producto->refresh();
    expect($producto->imagen)->not->toBe($rutaAnterior);
    Storage::disk('public')->assertMissing($rutaAnterior);
    Storage::disk('public')->assertExists($producto->imagen);
});

test('una tienda no puede modificar productos ni archivos de otra tienda', function () {
    Storage::fake('public');

    $tiendaA = Tienda::factory()->create();
    $tiendaB = Tienda::factory()->create();
    $adminA = administradorDeTienda($tiendaA);
    $categoriaB = Categoria::factory()->create(['id_tienda' => $tiendaB->id]);
    $productoB = Productos::factory()->create([
        'id_tienda' => $tiendaB->id,
        'id_categoria' => $categoriaB->id,
        'imagen' => "tiendas/{$tiendaB->id}/productos/privada.jpg",
    ]);

    $response = $this->actingAs($adminA)->put(route('productos.update', $productoB->id), [
        'nombre' => 'Intento de modificación',
        'precio' => 1,
        'stock' => 1,
        'estado' => 1,
        'id_categoria' => $categoriaB->id,
        'imagen' => UploadedFile::fake()->image('intrusa.png'),
    ]);

    $response->assertForbidden();
    expect($productoB->fresh()->nombre)->not->toBe('Intento de modificación');
    expect(Storage::disk('public')->allFiles("tiendas/{$tiendaB->id}/productos"))->toBe([]);
});

test('un administrador puede cargar y quitar el logo de su tienda', function () {
    Storage::fake('public');

    $tienda = Tienda::factory()->create();
    $admin = administradorDeTienda($tienda);

    $response = $this->actingAs($admin)->put(route('tiendas.update', $tienda->id), [
        'nombre' => $tienda->nombre,
        'estado' => 1,
        'logo' => UploadedFile::fake()->image('logo.png'),
    ]);

    $response->assertRedirect(route('tiendas.index'));
    $tienda->refresh();

    expect($tienda->logo)->toStartWith("tiendas/{$tienda->id}/logo/");
    Storage::disk('public')->assertExists($tienda->logo);

    $rutaLogo = $tienda->logo;

    $this->actingAs($admin)->put(route('tiendas.update', $tienda->id), [
        'nombre' => $tienda->nombre,
        'estado' => 1,
        'eliminar_logo' => 1,
    ])->assertRedirect(route('tiendas.index'));

    expect($tienda->fresh()->logo)->toBeNull();
    Storage::disk('public')->assertMissing($rutaLogo);
});

test('el registro público permite cargar el logo inicial de la tienda', function () {
    Storage::fake('public');

    $response = $this->post(route('registro.tienda.store'), [
        'nombre_tienda' => 'Tienda con logo de prueba',
        'nombre' => 'Ana',
        'apellido' => 'Prueba',
        'documento' => '8877665544',
        'correo' => 'ana.logo@prueba.test',
        'password' => 'ClaveSegura123!',
        'password_confirmation' => 'ClaveSegura123!',
        'logo' => UploadedFile::fake()->image('logo-inicial.png'),
    ]);

    $response->assertRedirect(route('dashboard.index'));

    $tienda = Tienda::query()->where('nombre', 'Tienda con logo de prueba')->firstOrFail();

    expect($tienda->logo)->toStartWith("tiendas/{$tienda->id}/logo/");
    Storage::disk('public')->assertExists($tienda->logo);
});

test('las cuentas demostrativas reciben logos e imágenes propias por tienda', function () {
    Storage::fake('public');

    $this->seed(TiendaSeeder::class);
    $this->seed(CuentasDemostracionSeeder::class);

    $tiendasDemo = Tienda::query()
        ->whereIn('correo', ['contacto@horizonte.void.test', 'contacto@nova.void.test'])
        ->get();

    expect($tiendasDemo)->toHaveCount(2);

    foreach ($tiendasDemo as $tienda) {
        expect($tienda->logo)->toStartWith("tiendas/{$tienda->id}/logo/");
        Storage::disk('public')->assertExists($tienda->logo);

        $productos = Productos::query()->where('id_tienda', $tienda->id)->get();
        expect($productos)->not->toBeEmpty();

        foreach ($productos as $producto) {
            expect($producto->imagen)->toStartWith("tiendas/{$tienda->id}/productos/");
            Storage::disk('public')->assertExists($producto->imagen);
        }
    }
});
