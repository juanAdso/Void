<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Admin') }}</title>

    {{-- Tailwind CSS. Reemplazar por tu build de Vite/PostCSS en producción (ver README). --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd',
                            400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8',
                            800: '#1e40af', 900: '#1e3a8a'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Chart.js (solo se usa donde se necesita) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js" defer></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }

        /* Navegación horizontal visible para tablas anchas */
        .table-scroll-area {
            max-width: 100%;
            overflow-x: auto !important;
            overflow-y: hidden;
            overscroll-behavior-x: contain;
            scrollbar-width: auto;
            scrollbar-color: #64748b #e2e8f0;
            -webkit-overflow-scrolling: touch;
        }
        .table-scroll-area::-webkit-scrollbar { height: 12px; }
        .table-scroll-area::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 999px; }
        .table-scroll-area::-webkit-scrollbar-thumb { background: #64748b; border: 2px solid #e2e8f0; border-radius: 999px; }
        .table-scroll-toolbar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: .5rem;
            margin: .5rem 0;
            color: #64748b;
            font-size: .75rem;
        }
        .table-scroll-toolbar button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            min-height: 2rem;
            padding: .25rem .65rem;
            border: 1px solid #cbd5e1;
            border-radius: .5rem;
            background: #fff;
            color: #334155;
            font-weight: 600;
            cursor: pointer;
        }
        .table-scroll-toolbar button:hover:not(:disabled) { background: #eff6ff; border-color: #60a5fa; color: #1d4ed8; }
        .table-scroll-toolbar button:disabled { opacity: .35; cursor: not-allowed; }
        @media (max-width: 640px) {
            .table-scroll-toolbar { justify-content: space-between; }
            .table-scroll-toolbar .table-scroll-hint { margin-right: auto; }
        }
    </style>

    @stack('styles')
</head>
<body class="h-full bg-slate-50 text-slate-800 antialiased">

    <div x-data="{ sidebarOpen: false }" class="min-h-screen flex">

        {{-- Overlay móvil --}}
        <div
            x-show="sidebarOpen"
            x-cloak
            @click="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden"
            aria-hidden="true"
        ></div>

        @include('layouts.partials.sidebar')

        <div class="flex-1 flex flex-col min-w-0 lg:pl-64">
            @include('layouts.partials.navbar')

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @if (session('success'))
                    <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
                @endif
                @if (session('error'))
                    <x-alert type="error" class="mb-6">{{ session('error') }}</x-alert>
                @endif

                @yield('content')
            </main>

            @include('layouts.partials.footer')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js" defer></script>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Aplica controles a las tablas de todos los módulos, incluidas las tablas reutilizables.
            document.querySelectorAll('main table').forEach(function (table) {
                let area = table.closest('.table-scroll-area');

                if (!area) {
                    area = table.closest('.overflow-x-auto');
                    if (!area) {
                        area = document.createElement('div');
                        table.parentNode.insertBefore(area, table);
                        area.appendChild(table);
                    }
                    area.classList.add('table-scroll-area');
                }

                if (area.dataset.scrollControlsReady === 'true') return;
                area.dataset.scrollControlsReady = 'true';

                const toolbar = document.createElement('div');
                toolbar.className = 'table-scroll-toolbar';
                toolbar.innerHTML = `
                    <span class="table-scroll-hint">Desliza para ver todas las columnas</span>
                    <button type="button" class="table-scroll-left" aria-label="Desplazar tabla a la izquierda" title="Ver columnas anteriores">←</button>
                    <button type="button" class="table-scroll-right" aria-label="Desplazar tabla a la derecha" title="Ver más columnas">→</button>
                `;
                area.parentNode.insertBefore(toolbar, area);

                const left = toolbar.querySelector('.table-scroll-left');
                const right = toolbar.querySelector('.table-scroll-right');
                const updateControls = function () {
                    const maxScroll = area.scrollWidth - area.clientWidth;
                    left.disabled = area.scrollLeft <= 2;
                    right.disabled = area.scrollLeft >= maxScroll - 2;
                    toolbar.hidden = maxScroll <= 2;
                };

                left.addEventListener('click', function () {
                    area.scrollBy({ left: -Math.max(240, area.clientWidth * 0.75), behavior: 'smooth' });
                });
                right.addEventListener('click', function () {
                    area.scrollBy({ left: Math.max(240, area.clientWidth * 0.75), behavior: 'smooth' });
                });
                area.addEventListener('scroll', updateControls, { passive: true });
                window.addEventListener('resize', updateControls);
                updateControls();
            });
        });
    </script>
</body>
</html>
