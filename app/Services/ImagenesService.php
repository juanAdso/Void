<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImagenesService
{
    public function guardarProducto(UploadedFile $archivo, int $tiendaId): string
    {
        return $archivo->store("tiendas/{$tiendaId}/productos", 'public');
    }

    public function guardarLogo(UploadedFile $archivo, int $tiendaId): string
    {
        return $archivo->store("tiendas/{$tiendaId}/logo", 'public');
    }

    public function eliminarProducto(?string $ruta, int $tiendaId): void
    {
        $this->eliminarSiPertenece($ruta, "tiendas/{$tiendaId}/productos");
    }

    public function eliminarLogo(?string $ruta, int $tiendaId): void
    {
        $this->eliminarSiPertenece($ruta, "tiendas/{$tiendaId}/logo");
    }

    private function eliminarSiPertenece(?string $ruta, string $directorio): void
    {
        if (
            ! $ruta
            || str_contains($ruta, '..')
            || ! str_starts_with($ruta, $directorio.'/')
        ) {
            return;
        }

        Storage::disk('public')->delete($ruta);
    }
}
