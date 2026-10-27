<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Productos;
use App\Models\Tienda;
use Illuminate\Database\Eloquent\Factories\Factory;
use RuntimeException;

class ProductosFactory extends Factory
{
    protected $model = Productos::class;

    public function definition(): array
    {
        $categoria = Categoria::query()->inRandomOrder()->first();
        $tienda = Tienda::query()->inRandomOrder()->first();

        if (!$categoria) {
            throw new RuntimeException('Debes crear las categorías antes de generar productos.');
        }

        if (!$tienda) {
            throw new RuntimeException('Debes crear las tiendas antes de generar productos.');
        }

        $catalogo = [
            'Tecnología' => [
                ['nombre' => 'Audífonos inalámbricos', 'min' => 40000, 'max' => 250000],
                ['nombre' => 'Teclado mecánico', 'min' => 80000, 'max' => 350000],
                ['nombre' => 'Mouse gamer', 'min' => 30000, 'max' => 180000],
                ['nombre' => 'Cargador USB', 'min' => 20000, 'max' => 100000],
                ['nombre' => 'Parlante portátil', 'min' => 50000, 'max' => 300000],
            ],
            'Ropa' => [
                ['nombre' => 'Camiseta básica', 'min' => 25000, 'max' => 80000],
                ['nombre' => 'Pantalón jean', 'min' => 60000, 'max' => 180000],
                ['nombre' => 'Buzo oversize', 'min' => 50000, 'max' => 150000],
                ['nombre' => 'Chaqueta casual', 'min' => 90000, 'max' => 300000],
            ],
            'Calzado' => [
                ['nombre' => 'Tenis deportivos', 'min' => 80000, 'max' => 350000],
                ['nombre' => 'Botas casuales', 'min' => 100000, 'max' => 400000],
                ['nombre' => 'Sandalias', 'min' => 20000, 'max' => 100000],
            ],
            'Hogar' => [
                ['nombre' => 'Organizador plástico', 'min' => 15000, 'max' => 70000],
                ['nombre' => 'Lámpara de mesa', 'min' => 30000, 'max' => 150000],
                ['nombre' => 'Juego de recipientes', 'min' => 20000, 'max' => 90000],
            ],
            'Deportes' => [
                ['nombre' => 'Balón deportivo', 'min' => 30000, 'max' => 150000],
                ['nombre' => 'Mancuernas', 'min' => 40000, 'max' => 200000],
                ['nombre' => 'Botella deportiva', 'min' => 15000, 'max' => 60000],
            ],
            'Accesorios' => [
                ['nombre' => 'Gorra urbana', 'min' => 20000, 'max' => 80000],
                ['nombre' => 'Mochila casual', 'min' => 40000, 'max' => 180000],
                ['nombre' => 'Billetera', 'min' => 20000, 'max' => 100000],
            ],
            'Belleza' => [
                ['nombre' => 'Crema hidratante', 'min' => 15000, 'max' => 100000],
                ['nombre' => 'Kit de cuidado facial', 'min' => 40000, 'max' => 180000],
            ],
            'Electrodomésticos' => [
                ['nombre' => 'Licuadora', 'min' => 100000, 'max' => 400000],
                ['nombre' => 'Cafetera eléctrica', 'min' => 80000, 'max' => 350000],
                ['nombre' => 'Ventilador', 'min' => 90000, 'max' => 300000],
            ],
            'Papelería' => [
                ['nombre' => 'Cuaderno', 'min' => 5000, 'max' => 25000],
                ['nombre' => 'Juego de marcadores', 'min' => 10000, 'max' => 50000],
                ['nombre' => 'Agenda', 'min' => 15000, 'max' => 60000],
            ],
            'Juguetes' => [
                ['nombre' => 'Juego de construcción', 'min' => 30000, 'max' => 180000],
                ['nombre' => 'Carro de juguete', 'min' => 10000, 'max' => 80000],
            ],
            'Muebles' => [
                ['nombre' => 'Mesa auxiliar', 'min' => 80000, 'max' => 350000],
                ['nombre' => 'Silla de escritorio', 'min' => 100000, 'max' => 500000],
            ],
            'Libros' => [
                ['nombre' => 'Novela', 'min' => 25000, 'max' => 90000],
                ['nombre' => 'Libro educativo', 'min' => 30000, 'max' => 120000],
            ],
            'Ferretería' => [
                ['nombre' => 'Juego de destornilladores', 'min' => 20000, 'max' => 100000],
                ['nombre' => 'Martillo', 'min' => 15000, 'max' => 70000],
            ],
            'Automotriz' => [
                ['nombre' => 'Soporte para celular', 'min' => 20000, 'max' => 90000],
                ['nombre' => 'Cargador para vehículo', 'min' => 25000, 'max' => 100000],
            ],
            'Mascotas' => [
                ['nombre' => 'Cama para mascota', 'min' => 40000, 'max' => 200000],
                ['nombre' => 'Juguete para mascota', 'min' => 10000, 'max' => 60000],
                ['nombre' => 'Plato para mascota', 'min' => 10000, 'max' => 50000],
            ],
        ];

        $nombreCategoria = $categoria->nombre_categoria;
        $opciones = $catalogo[$nombreCategoria] ?? [
            ['nombre' => 'Producto general', 'min' => 10000, 'max' => 150000],
        ];
        $producto = fake()->randomElement($opciones);

        return [
            'nombre' => $producto['nombre'].' '.fake()->unique()->numberBetween(1000, 999999),
            'descripcion' => 'Producto de la categoría '.$nombreCategoria.'.',
            'precio' => fake()->randomFloat(2, $producto['min'], $producto['max']),
            'stock' => fake()->numberBetween(1, 100),
            'imagen' => null,
            'estado' => true,
            'id_categoria' => $categoria->getKey(),
            'id_tienda' => $tienda->getKey(),
        ];
    }
}
