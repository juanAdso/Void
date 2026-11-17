<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\DetalleFactura;
use App\Models\Facturas;
use App\Models\MovimientoInventario;
use App\Models\Productos;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class CuentasDemostracionSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production') && ! (bool) env('VOID_DEMO_DATA', false)) {
            return;
        }

        $tiendaHorizonte = Tienda::query()
            ->where('correo', 'contacto@horizonte.void.test')
            ->firstOrFail();

        $tiendaNova = Tienda::query()
            ->where('correo', 'contacto@nova.void.test')
            ->firstOrFail();

        $password = (string) env('VOID_DEMO_PASSWORD', 'VoidDemo2026!');

        if (strlen($password) < 8) {
            throw new RuntimeException('VOID_DEMO_PASSWORD debe tener al menos 8 caracteres.');
        }

        $horizonteAdmin = $this->crearCuenta(
            $tiendaHorizonte, 'Laura', 'Martínez', 'DEMO-HZ-ADMIN', 'admin.horizonte@void.test', 'administrador', $password
        );
        $horizonteEmpleado = $this->crearCuenta(
            $tiendaHorizonte, 'Camila', 'Rojas', 'DEMO-HZ-EMP', 'empleado.horizonte@void.test', 'empleado', $password
        );

        $novaAdmin = $this->crearCuenta(
            $tiendaNova, 'Andrés', 'Gómez', 'DEMO-NV-ADMIN', 'admin.nova@void.test', 'administrador', $password
        );
        $novaEmpleado = $this->crearCuenta(
            $tiendaNova, 'Diego', 'Pérez', 'DEMO-NV-EMP', 'empleado.nova@void.test', 'empleado', $password
        );

        $this->crearDatosComerciales($tiendaHorizonte, $horizonteAdmin, $horizonteEmpleado, 'HZ');
        $this->crearDatosComerciales($tiendaNova, $novaAdmin, $novaEmpleado, 'NV');
    }

    private function crearCuenta(
        Tienda $tienda,
        string $nombre,
        string $apellido,
        string $documento,
        string $correo,
        string $rol,
        string $password
    ): User {
        $usuario = User::firstOrCreate(
            ['correo' => $correo],
            [
                'nombre' => $nombre,
                'apellido' => $apellido,
                'documento' => $documento,
                'password' => Hash::make($password),
                'rol' => $rol,
                'id_tienda' => $tienda->id,
                'estado' => true,
            ]
        );

        if ((int) $usuario->id_tienda !== (int) $tienda->id || $usuario->rol !== $rol) {
            throw new RuntimeException("La cuenta {$correo} ya existe con otra empresa o rol. No se modificó para evitar reasignar una cuenta real.");
        }

        $usuario->syncRoles([$rol]);

        return $usuario;
    }

    private function crearDatosComerciales(Tienda $tienda, User $administrador, User $empleado, string $prefijo): void
    {
        $categorias = [];
        foreach ([
            ['nombre' => 'Tecnología', 'descripcion' => 'Dispositivos y accesorios tecnológicos.'],
            ['nombre' => 'Accesorios', 'descripcion' => 'Complementos para uso diario.'],
            ['nombre' => 'Hogar', 'descripcion' => 'Artículos prácticos para el hogar.'],
        ] as $datos) {
            $categorias[$datos['nombre']] = Categoria::firstOrCreate(
                ['id_tienda' => $tienda->id, 'nombre' => $datos['nombre']],
                ['descripcion' => $datos['descripcion'], 'estado' => true]
            );
        }

        $clientes = [];
        foreach ([
            ['nombre' => 'Mariana', 'apellido' => 'Torres', 'documento' => "{$prefijo}-CLI-001", 'telefono' => '3005551001', 'correo' => "cliente1.{$prefijo}@void.test"],
            ['nombre' => 'Felipe', 'apellido' => 'Ramírez', 'documento' => "{$prefijo}-CLI-002", 'telefono' => '3005551002', 'correo' => "cliente2.{$prefijo}@void.test"],
            ['nombre' => 'Valentina', 'apellido' => 'Castro', 'documento' => "{$prefijo}-CLI-003", 'telefono' => '3005551003', 'correo' => "cliente3.{$prefijo}@void.test"],
        ] as $datos) {
            $clientes[] = Cliente::firstOrCreate(
                ['id_tienda' => $tienda->id, 'documento' => $datos['documento']],
                $datos
            );
        }

        if (! $tienda->logo) {
            $rutaLogo = "tiendas/{$tienda->id}/logo/demo-".Str::slug($prefijo).'.svg';
            Storage::disk('public')->put($rutaLogo, $this->crearSvgLogo($tienda->nombre, $prefijo));
            $tienda->update(['logo' => $rutaLogo]);
        }

        $productos = [];
        foreach ([
            ['nombre' => 'Audífonos inalámbricos', 'categoria' => 'Tecnología', 'precio' => 129900, 'stock' => 24],
            ['nombre' => 'Teclado mecánico', 'categoria' => 'Tecnología', 'precio' => 189900, 'stock' => 15],
            ['nombre' => 'Mouse ergonómico', 'categoria' => 'Accesorios', 'precio' => 79900, 'stock' => 32],
            ['nombre' => 'Lámpara de escritorio', 'categoria' => 'Hogar', 'precio' => 65900, 'stock' => 18],
        ] as $datos) {
            $producto = Productos::firstOrCreate(
                ['id_tienda' => $tienda->id, 'nombre' => $datos['nombre']],
                [
                    'id_categoria' => $categorias[$datos['categoria']]->id,
                    'descripcion' => "Producto de demostración para {$tienda->nombre}.",
                    'precio' => $datos['precio'],
                    'stock' => $datos['stock'],
                    'imagen' => null,
                    'estado' => true,
                ]
            );

            if (! $producto->imagen) {
                $rutaImagen = "tiendas/{$tienda->id}/productos/demo-".Str::slug($producto->nombre).'.svg';
                Storage::disk('public')->put(
                    $rutaImagen,
                    $this->crearSvgProducto($producto->nombre, $tienda->nombre, count($productos))
                );
                $producto->update(['imagen' => $rutaImagen]);
            }

            $productos[] = $producto;

            MovimientoInventario::firstOrCreate(
                [
                    'id_producto' => $producto->id,
                    'tipo_movimiento' => 'entrada',
                    'motivo' => 'Carga inicial de demostración',
                ],
                [
                    'id_tienda' => $tienda->id,
                    'id_usuario' => $administrador->id,
                    'cantidad' => $datos['stock'],
                    'fecha' => now(),
                ]
            );
        }

        $this->crearFacturaDemostracion($empleado, $clientes[0], $productos[0], $productos[2], now()->toDateString());
        $this->crearFacturaDemostracion($administrador, $clientes[1], $productos[1], $productos[3], now()->subDays(3)->toDateString());
        $this->crearFacturaDemostracion($empleado, $clientes[2], $productos[0], $productos[1], now()->subDays(8)->toDateString());
    }

    private function crearSvgLogo(string $nombre, string $prefijo): string
    {
        $inicial = htmlspecialchars(strtoupper(substr($prefijo, 0, 1)), ENT_QUOTES | ENT_XML1, 'UTF-8');
        $nombreSeguro = htmlspecialchars($nombre, ENT_QUOTES | ENT_XML1, 'UTF-8');

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512">
    <defs>
        <linearGradient id="fondo" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#111827"/>
            <stop offset="1" stop-color="#1d4ed8"/>
        </linearGradient>
    </defs>
    <rect width="512" height="512" rx="84" fill="url(#fondo)"/>
    <circle cx="256" cy="210" r="132" fill="none" stroke="#fbbf24" stroke-width="14"/>
    <path d="M170 260h172M200 300h112" stroke="#fbbf24" stroke-width="12" stroke-linecap="round"/>
    <text x="256" y="238" text-anchor="middle" font-family="Arial,sans-serif" font-size="150" font-weight="700" fill="#ffffff">{$inicial}</text>
    <text x="256" y="408" text-anchor="middle" font-family="Arial,sans-serif" font-size="24" font-weight="700" fill="#ffffff">{$nombreSeguro}</text>
</svg>
SVG;
    }

    private function crearSvgProducto(string $nombre, string $tienda, int $indice): string
    {
        $colores = ['#2563eb', '#d97706', '#0f766e', '#7c3aed'];
        $color = $colores[$indice % count($colores)];
        $nombreSeguro = htmlspecialchars($nombre, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $tiendaSegura = htmlspecialchars($tienda, ENT_QUOTES | ENT_XML1, 'UTF-8');

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="600" viewBox="0 0 800 600">
    <defs>
        <linearGradient id="fondo" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#f8fafc"/>
            <stop offset="1" stop-color="#e2e8f0"/>
        </linearGradient>
    </defs>
    <rect width="800" height="600" fill="url(#fondo)"/>
    <circle cx="400" cy="245" r="150" fill="{$color}" opacity=".12"/>
    <circle cx="400" cy="245" r="112" fill="none" stroke="{$color}" stroke-width="18"/>
    <rect x="335" y="178" width="130" height="135" rx="28" fill="{$color}"/>
    <path d="M365 210h70M365 238h70M365 266h48" stroke="#ffffff" stroke-width="12" stroke-linecap="round"/>
    <text x="400" y="465" text-anchor="middle" font-family="Arial,sans-serif" font-size="35" font-weight="700" fill="#0f172a">{$nombreSeguro}</text>
    <text x="400" y="515" text-anchor="middle" font-family="Arial,sans-serif" font-size="22" fill="#475569">{$tiendaSegura} · Imagen de prueba</text>
</svg>
SVG;
    }

    private function crearFacturaDemostracion(
        User $usuario,
        Cliente $cliente,
        Productos $productoUno,
        Productos $productoDos,
        string $fecha
    ): void {
        $factura = Facturas::firstOrCreate(
            [
                'id_usuario' => $usuario->id,
                'id_cliente' => $cliente->id,
                'fecha' => $fecha,
            ],
            [
                'subtotal' => 0,
                'iva' => 0,
                'total' => 0,
                'estado' => 'pagada',
            ]
        );

        foreach ([[$productoUno, 1], [$productoDos, 1]] as [$producto, $cantidad]) {
            DetalleFactura::firstOrCreate(
                ['id_factura' => $factura->id, 'id_producto' => $producto->id],
                [
                    'cantidad' => $cantidad,
                    'precio_unitario' => $producto->precio,
                    'subtotal' => round((float) $producto->precio * $cantidad, 2),
                ]
            );
        }

        $subtotal = round((float) $factura->detalle_factura()->sum('subtotal'), 2);
        $iva = round($subtotal * 0.19, 2);

        $factura->update([
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => round($subtotal + $iva, 2),
        ]);
    }
}
