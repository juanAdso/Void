<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre_categoria' => 'Tecnología', 'descripcion' => 'Computadores, accesorios y dispositivos electrónicos.'],
            ['nombre_categoria' => 'Ropa', 'descripcion' => 'Prendas de vestir para diferentes estilos y ocasiones.'],
            ['nombre_categoria' => 'Calzado', 'descripcion' => 'Zapatos, tenis, botas y sandalias.'],
            ['nombre_categoria' => 'Hogar', 'descripcion' => 'Productos para equipar y organizar el hogar.'],
            ['nombre_categoria' => 'Deportes', 'descripcion' => 'Artículos y accesorios deportivos.'],
            ['nombre_categoria' => 'Accesorios', 'descripcion' => 'Complementos de uso personal y cotidiano.'],
            ['nombre_categoria' => 'Belleza', 'descripcion' => 'Productos de cuidado personal y belleza.'],
            ['nombre_categoria' => 'Electrodomésticos', 'descripcion' => 'Equipos eléctricos para el hogar.'],
            ['nombre_categoria' => 'Papelería', 'descripcion' => 'Útiles escolares y artículos de oficina.'],
            ['nombre_categoria' => 'Juguetes', 'descripcion' => 'Juguetes y artículos recreativos.'],
            ['nombre_categoria' => 'Muebles', 'descripcion' => 'Muebles para hogares y espacios de trabajo.'],
            ['nombre_categoria' => 'Libros', 'descripcion' => 'Libros y material de lectura.'],
            ['nombre_categoria' => 'Ferretería', 'descripcion' => 'Herramientas y materiales para reparaciones.'],
            ['nombre_categoria' => 'Automotriz', 'descripcion' => 'Accesorios y productos para vehículos.'],
            ['nombre_categoria' => 'Mascotas', 'descripcion' => 'Productos para el cuidado de animales.'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::firstOrCreate(
                ['nombre_categoria' => $categoria['nombre_categoria']],
                ['descripcion' => $categoria['descripcion'], 'estado' => true]
            );
        }
    }
}
