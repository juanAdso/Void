<?php

namespace Database\Seeders;

use App\Models\Tienda;
use Illuminate\Database\Seeder;

class TiendaSeeder extends Seeder
{
    public function run(): void
    {
        // Los datos de exposición se crean en desarrollo y nunca se insertan en producción
        // salvo que el responsable habilite VOID_DEMO_DATA explícitamente.
        if (app()->environment('production') && ! (bool) env('VOID_DEMO_DATA', false)) {
            return;
        }

        Tienda::firstOrCreate(
            ['correo' => 'contacto@horizonte.void.test'],
            [
                'nombre' => 'Comercial Horizonte S.A.S.',
                'descripcion' => 'Empresa de comercio minorista con catálogo de tecnología, accesorios y hogar.',
                'direccion' => 'Carrera 15 # 24-30, Bucaramanga',
                'telefono' => '3005550101',
                'estado' => true,
            ]
        );

        Tienda::firstOrCreate(
            ['correo' => 'contacto@nova.void.test'],
            [
                'nombre' => 'Tecnología Nova S.A.S.',
                'descripcion' => 'Tienda especializada en tecnología, periféricos y productos electrónicos.',
                'direccion' => 'Calle 36 # 18-42, Bucaramanga',
                'telefono' => '3005550202',
                'estado' => true,
            ]
        );
    }
}
