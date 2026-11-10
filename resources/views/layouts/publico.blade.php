<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Catálogo') | VOID</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }
    </style>

    @stack('styles')
</head>

<body class="min-h-screen bg-slate-50 text-neutral-950 antialiased flex flex-col justify-between">

    {{-- Menú público superior --}}
    <header class="sticky top-0 z-50 border-b border-neutral-800 bg-neutral-950/95 backdrop-blur text-amber-50">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-4 lg:px-8">

            {{-- Logo --}}
            <a href="{{ url('/') }}"
               class="text-2xl font-extrabold tracking-widest text-amber-50">
                V<span class="text-blue-500">Ø</span>ID
            </a>

            {{-- Navegación para computador --}}
            <div class="hidden items-center gap-8 md:flex">
                <a href="{{ url('/#tiendas') }}"
                   class="text-sm font-medium text-slate-300 transition hover:text-blue-400">
                    Tiendas
                </a>

                <a href="{{ route('catalogo') }}"
                   class="text-sm font-medium text-slate-300 transition hover:text-blue-400">
                    Catálogos
                </a>

                <a href="{{ route('registro.tienda') }}"
                   class="text-sm font-medium text-slate-300 transition hover:text-blue-400">
                    Registrar tienda
                </a>
            </div>

            {{-- Acceso administrativo --}}
            <a href="{{ route('login') }}"
               class="shrink-0 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-amber-50 transition hover:bg-blue-500 shadow-sm">
                Iniciar sesión
            </a>
        </nav>
    </header>

    {{-- Contenido de cada página pública --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Pie de página --}}
    <footer class="border-t border-neutral-800 bg-neutral-950 text-amber-50">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-5 py-10 sm:flex-row sm:items-center sm:justify-between lg:px-8">

            <div>
                <p class="text-xl font-extrabold tracking-widest text-amber-50">
                    V<span class="text-blue-500">Ø</span>ID
                </p>
                <p class="mt-2 text-sm text-slate-400">
                    Explora los catálogos y productos exclusivos de cada tienda.
                </p>
            </div>

            <div class="flex flex-wrap gap-5 text-sm text-slate-300">
                <a href="{{ url('/') }}" class="hover:text-blue-400 transition">
                    Inicio
                </a>

                <a href="{{ url('/#tiendas') }}" class="hover:text-blue-400 transition">
                    Tiendas
                </a>

                <a href="{{ route('catalogo') }}" class="hover:text-blue-400 transition">
                    Catálogos
                </a>

                <a href="{{ route('registro.tienda') }}" class="hover:text-blue-400 transition">
                    Registrar tienda
                </a>
            </div>

            <p class="text-xs text-slate-500">
                &copy; {{ date('Y') }} VOID. Todos los derechos reservados.
            </p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
