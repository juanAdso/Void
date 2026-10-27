
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

<body class="min-h-screen bg-white text-gray-900 antialiased">

    {{-- Menú público superior --}}
    <header class="sticky top-0 z-50 border-b border-gray-200 bg-white/95 backdrop-blur">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-4 lg:px-8">

            {{-- Logo --}}
            <a href="{{ url('/') }}"
               class="text-2xl font-extrabold tracking-widest">
                V<span class="text-violet-600">Ø</span>ID
            </a>

            {{-- Navegación para computador --}}
            <div class="hidden items-center gap-8 md:flex">
                <a href="{{ url('/#tiendas') }}"
                   class="text-sm font-medium text-gray-700 transition hover:text-violet-600">
                    Tiendas
                </a>

                <a href="{{ url('/#productos') }}"
                   class="text-sm font-medium text-gray-700 transition hover:text-violet-600">
                    Catálogo
                </a>
            </div>

            {{-- Acceso administrativo temporal --}}
            <a href="{{ route('login') }}"
               class="shrink-0 rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-violet-700">
                Iniciar sesión
            </a>
        </nav>
    </header>

    {{-- Contenido de cada página pública --}}
    <main>
        @yield('content')
    </main>

    {{-- Pie de página --}}
    <footer class="mt-16 border-t border-gray-200 bg-gray-950 text-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-5 py-10 sm:flex-row sm:items-center sm:justify-between lg:px-8">

            <div>
                <p class="text-xl font-extrabold tracking-widest">
                    V<span class="text-violet-400">Ø</span>ID
                </p>
                <p class="mt-2 text-sm text-gray-400">
                    Explora los catálogos de diferentes tiendas.
                </p>
            </div>

            <div class="flex flex-wrap gap-5 text-sm text-gray-300">
                <a href="{{ url('/') }}" class="hover:text-white">
                    Inicio
                </a>

                <a href="{{ url('/#tiendas') }}" class="hover:text-white">
                    Tiendas
                </a>

                <a href="{{ url('/#productos') }}" class="hover:text-white">
                    Productos
                </a>
            </div>

            <p class="text-xs text-gray-500">
                © {{ date('Y') }} VOID. Todos los derechos reservados.
            </p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
