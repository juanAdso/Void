<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Tecnología', 'descripcion' => 'Computadores, accesorios y dispositivos electrónicos.'],
            ['nombre' => 'Ropa', 'descripcion' => 'Prendas de vestir para diferentes estilos y ocasiones.'],
            ['nombre' => 'Calzado', 'descripcion' => 'Zapatos, tenis, botas y sandalias.'],
            ['nombre' => 'Hogar', 'descripcion' => 'Productos para equipar y organizar el hogar.'],
            ['nombre' => 'Deportes', 'descripcion' => 'Artículos y accesorios deportivos.'],
            ['nombre' => 'Accesorios', 'descripcion' => 'Complementos de uso personal y cotidiano.'],
            ['nombre' => 'Belleza', 'descripcion' => 'Productos de cuidado personal y belleza.'],
            ['nombre' => 'Electrodomésticos', 'descripcion' => 'Equipos eléctricos para el hogar.'],
            ['nombre' => 'Papelería', 'descripcion' => 'Útiles escolares y artículos de oficina.'],
            ['nombre' => 'Juguetes', 'descripcion' => 'Juguetes y artículos recreativos.'],
            ['nombre' => 'Muebles', 'descripcion' => 'Muebles para hogares y espacios de trabajo.'],
            ['nombre' => 'Libros', 'descripcion' => 'Libros y material de lectura.'],
            ['nombre' => 'Ferretería', 'descripcion' => 'Herramientas y materiales para reparaciones.'],
            ['nombre' => 'Automotriz', 'descripcion' => 'Accesorios y productos para vehículos.'],
            ['nombre' => 'Mascotas', 'descripcion' => 'Productos para el cuidado de animales.'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::firstOrCreate(
                ['nombre' => $categoria['nombre']],
                ['descripcion' => $categoria['descripcion'], 'estado' => true]
            );
        }
    }
}
