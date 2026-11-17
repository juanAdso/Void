<?php

declare(strict_types=1);

function limpiarComentariosPhp(string $contenido): string
{
    $tokens = token_get_all($contenido);
    $resultado = '';

    foreach ($tokens as $token) {
        if (! is_array($token)) {
            $resultado .= $token;

            continue;
        }

        [$tipo, $texto] = $token;

        if (in_array($tipo, [T_COMMENT, T_DOC_COMMENT], true)) {
            $saltos = substr_count($texto, "\n");
            $resultado .= $saltos > 0 ? str_repeat("\n", $saltos) : ' ';

            continue;
        }

        $resultado .= $texto;
    }

    return $resultado;
}

function procesarArchivo(string $ruta): void
{
    $contenido = file_get_contents($ruta);

    if ($contenido === false) {
        throw new RuntimeException("No se pudo leer {$ruta}.");
    }

    $original = $contenido;
    $extension = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));
    $esBlade = str_ends_with($ruta, '.blade.php');

    if ($esBlade) {
        $contenido = preg_replace('/\{\{--[\s\S]*?--\}\}/', '', $contenido) ?? $contenido;
        $contenido = preg_replace('/<!--[\s\S]*?-->/', '', $contenido) ?? $contenido;
        $contenido = limpiarComentariosPhp($contenido);
    } elseif ($extension === 'php') {
        $contenido = limpiarComentariosPhp($contenido);
    } elseif (in_array($extension, ['md', 'markdown'], true)) {
        $contenido = preg_replace('/<!--[\s\S]*?-->/', '', $contenido) ?? $contenido;
    } elseif (basename($ruta) === '.env.example') {
        $lineas = preg_split('/\R/', $contenido) ?: [];
        $lineas = array_filter(
            $lineas,
            fn (string $linea): bool => ! str_starts_with(ltrim($linea), '#')
        );
        $contenido = implode("\n", $lineas);
    }

    $contenido = preg_replace('/[ \\t]+$/m', '', $contenido) ?? $contenido;

    if ($contenido !== $original && file_put_contents($ruta, $contenido) === false) {
        throw new RuntimeException("No se pudo escribir {$ruta}.");
    }
}

$raiz = dirname(__DIR__);
$excluidos = ['.git', 'vendor', 'node_modules', 'storage', 'public/build', 'bootstrap/cache'];
$directorios = new RecursiveDirectoryIterator($raiz, FilesystemIterator::SKIP_DOTS);
$archivos = new RecursiveIteratorIterator($directorios);

foreach ($archivos as $archivo) {
    if (! $archivo->isFile()) {
        continue;
    }

    $ruta = $archivo->getPathname();
    $relativa = str_replace('\\', '/', substr($ruta, strlen($raiz) + 1));

    foreach ($excluidos as $excluido) {
        if ($relativa === $excluido || str_starts_with($relativa, $excluido.'/')) {
            continue 2;
        }
    }

    if (str_ends_with($relativa, '.php')
        || in_array(strtolower(pathinfo($relativa, PATHINFO_EXTENSION)), ['md', 'markdown'], true)
        || basename($relativa) === '.env.example') {
        procesarArchivo($ruta);
    }
}
