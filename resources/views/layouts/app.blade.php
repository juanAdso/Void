<!DOCTYPE html>
<html lang="es" class="h-full bg-neutral-950">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Admin') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a'
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js" defer></script>

    <style>
        [x-cloak] {
            display: none !important
        }

        :root {
            color-scheme: dark;
            --void-bg: #0a0a0a;
            --void-amber: #f59e0b;
            --void-blue: #3b82f6
        }

        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            background: var(--void-bg);
            color: #f5f5f5
        }

        .void-admin main .bg-white {
            background: #171717 !important
        }

        .void-admin main .bg-slate-50 {
            background: #111 !important
        }

        .void-admin main .bg-slate-100 {
            background: #242424 !important
        }

        .void-admin main .bg-slate-200 {
            background: #303030 !important
        }

        .void-admin main .text-slate-900,
        .void-admin main .text-slate-800 {
            color: #f5f5f5 !important
        }

        .void-admin main .text-slate-700 {
            color: #e5e5e5 !important
        }

        .void-admin main .text-slate-600 {
            color: #c4c4c4 !important
        }

        .void-admin main .text-slate-500 {
            color: #a3a3a3 !important
        }

        .void-admin main .text-slate-400 {
            color: #858585 !important
        }

        .void-admin main .border-slate-100,
        .void-admin main .border-slate-200 {
            border-color: #303030 !important
        }

        .void-admin main .border-slate-300 {
            border-color: #454545 !important
        }

        .void-admin main .divide-slate-100>:not([hidden])~:not([hidden]),
        .void-admin main .divide-slate-200>:not([hidden])~:not([hidden]) {
            border-color: #2c2c2c !important
        }

        .void-admin main [class~="hover:bg-slate-50"]:hover {
            background: #242424 !important
        }

        .void-admin main [class~="hover:bg-slate-100"]:hover {
            background: #303030 !important
        }

        .void-admin main .bg-emerald-50 {
            background: #102b21 !important
        }

        .void-admin main .text-emerald-700 {
            color: #6ee7b7 !important
        }

        .void-admin main .border-emerald-200 {
            border-color: #14532d !important
        }

        .void-admin main .bg-red-50 {
            background: #301818 !important
        }

        .void-admin main .text-red-700,
        .void-admin main .text-red-600 {
            color: #fca5a5 !important
        }

        .void-admin main .border-red-200 {
            border-color: #7f1d1d !important
        }

        .void-admin main .bg-amber-50 {
            background: #33250f !important
        }

        .void-admin main .text-amber-700 {
            color: #fcd34d !important
        }

        .void-admin main .border-amber-200 {
            border-color: #854d0e !important
        }

        .void-admin main .bg-primary-50 {
            background: #172554 !important
        }

        .void-admin main .text-primary-700 {
            color: #93c5fd !important
        }

        .void-admin main .text-blue-600 {
            color: #60a5fa !important
        }

        .void-admin main .text-blue-800 {
            color: #93c5fd !important
        }

        .void-admin main input:not([type=checkbox]):not([type=radio]):not([type=file]),
        .void-admin main select,
        .void-admin main textarea {
            color: #f5f5f5;
            background: #111;
            border-color: #404040;
            color-scheme: dark
        }

        .void-admin main input::placeholder,
        .void-admin main textarea::placeholder {
            color: #737373
        }

        .void-admin main input:focus,
        .void-admin main select:focus,
        .void-admin main textarea:focus {
            border-color: var(--void-blue);
            outline: 0;
            box-shadow: 0 0 0 3px rgb(59 130 246 / 18%)
        }

        .void-admin main input[type=checkbox],
        .void-admin main input[type=radio] {
            accent-color: var(--void-blue)
        }

        .void-admin main table {
            color: #d4d4d4
        }

        .void-admin main thead th {
            color: #a3a3a3 !important
        }

        .void-admin main tbody tr {
            transition: background-color 180ms ease
        }

        @keyframes void-page-enter {
            from {
                opacity: 0;
                transform: translateY(9px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .void-admin .void-main>* {
            animation: void-page-enter 420ms cubic-bezier(.2, .7, .2, 1) both
        }

        .void-admin .void-main>*:nth-child(2) {
            animation-delay: 35ms
        }

        .void-admin .void-main>*:nth-child(3) {
            animation-delay: 70ms
        }

        .void-admin .void-main>*:nth-child(4) {
            animation-delay: 105ms
        }

        .void-card {
            transition: transform 200ms ease, border-color 200ms ease, box-shadow 200ms ease
        }

        .void-card:hover {
            transform: translateY(-2px);
            border-color: rgb(59 130 246 / 38%) !important;
            box-shadow: 0 14px 32px rgb(0 0 0 / 20%)
        }

        .void-stat-card {
            position: relative;
            overflow: hidden;
            transition: transform 200ms ease, border-color 200ms ease, box-shadow 200ms ease
        }

        .void-stat-card:after {
            content: "";
            position: absolute;
            inset: 0 0 auto;
            height: 2px;
            background: linear-gradient(90deg, var(--void-amber), var(--void-blue), transparent 85%);
            opacity: .8
        }

        .void-stat-card:hover {
            transform: translateY(-3px);
            border-color: rgb(245 158 11 / 42%) !important;
            box-shadow: 0 16px 34px rgb(0 0 0 / 24%)
        }

        .void-admin a,
        .void-admin button {
            transition-property: color, background-color, border-color, box-shadow, transform, opacity;
            transition-duration: 180ms
        }

        .void-admin main a[class*="bg-"]:hover,
        .void-admin main button[class*="bg-"]:not(:disabled):hover {
            transform: translateY(-1px)
        }

        .void-admin main button:active,
        .void-admin main a[class*="bg-"]:active {
            transform: scale(.98)
        }

        .void-page-eyebrow {
            color: var(--void-amber);
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .16em;
            text-transform: uppercase
        }

        .void-page-heading {
            border-bottom: 1px solid #262626;
            padding-bottom: 1.25rem
        }

        .void-admin aside nav a[aria-current=page] {
            border-left: 2px solid var(--void-amber)
        }

        .table-scroll-area {
            max-width: 100%;
            overflow-x: auto !important;
            overflow-y: hidden;
            overscroll-behavior-x: contain;
            scrollbar-width: auto;
            scrollbar-color: #525252 #171717;
            -webkit-overflow-scrolling: touch
        }

        .table-scroll-area::-webkit-scrollbar {
            height: 12px
        }

        .table-scroll-area::-webkit-scrollbar-track {
            background: #171717;
            border-radius: 999px
        }

        .table-scroll-area::-webkit-scrollbar-thumb {
            background: #525252;
            border: 2px solid #171717;
            border-radius: 999px
        }

        .table-scroll-toolbar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: .5rem;
            margin: .5rem 0;
            color: #a3a3a3;
            font-size: .75rem
        }

        .table-scroll-toolbar button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            min-height: 2rem;
            padding: .25rem .65rem;
            border: 1px solid #404040;
            border-radius: .5rem;
            background: #171717;
            color: #e5e5e5;
            font-weight: 600;
            cursor: pointer
        }

        .table-scroll-toolbar button:hover:not(:disabled) {
            background: #262626;
            border-color: var(--void-blue);
            color: #93c5fd
        }

        .table-scroll-toolbar button:disabled {
            opacity: .35;
            cursor: not-allowed
        }

        @media(max-width:640px) {
            .table-scroll-toolbar {
                justify-content: space-between
            }

            .table-scroll-toolbar .table-scroll-hint {
                margin-right: auto
            }
        }

        @media(prefers-reduced-motion:reduce) {

            .void-admin *,
            .void-admin *:before,
            .void-admin *:after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
                transition-duration: .01ms !important
            }
        }
    
        .void-admin .void-main {
            background:
                radial-gradient(900px circle at 4% 0, rgb(37 99 235 / 9%), transparent 48%),
                radial-gradient(760px circle at 96% 8%, rgb(245 158 11 / 6%), transparent 44%),
                #0b0f16;
        }

        .void-admin main {
            color: #f4f6fa;
        }

        .void-admin main .bg-white {
            background: #151b25 !important;
        }

        .void-admin main .bg-slate-50,
        .void-admin main .bg-gray-50 {
            background: #111821 !important;
        }

        .void-admin main .bg-slate-100,
        .void-admin main .bg-gray-100 {
            background: #1b2430 !important;
        }

        .void-admin main .bg-slate-200,
        .void-admin main .bg-gray-200 {
            background: #273342 !important;
        }

        .void-admin main [class~="text-neutral-500"],
        .void-admin main [class~="text-neutral-600"],
        .void-admin main [class~="text-slate-500"],
        .void-admin main [class~="text-slate-600"],
        .void-admin main [class~="text-gray-500"],
        .void-admin main [class~="text-gray-600"],
        .void-admin main [class~="text-zinc-500"],
        .void-admin main [class~="text-zinc-600"] {
            color: #b7c1d0 !important;
        }

        .void-admin main [class~="text-slate-400"],
        .void-admin main [class~="text-neutral-400"],
        .void-admin main [class~="text-gray-400"],
        .void-admin main [class~="text-zinc-400"] {
            color: #c5ceda !important;
        }

        .void-admin main [class~="text-slate-700"],
        .void-admin main [class~="text-slate-800"],
        .void-admin main [class~="text-slate-900"],
        .void-admin main [class~="text-gray-700"],
        .void-admin main [class~="text-gray-800"],
        .void-admin main [class~="text-gray-900"] {
            color: #edf1f7 !important;
        }

        .void-admin main .border-slate-100,
        .void-admin main .border-slate-200,
        .void-admin main .border-gray-100,
        .void-admin main .border-gray-200 {
            border-color: #303b4b !important;
        }

        .void-admin main .border-slate-300,
        .void-admin main .border-gray-300 {
            border-color: #405064 !important;
        }

        .void-admin main [class~="hover:bg-slate-50"]:hover,
        .void-admin main [class~="hover:bg-gray-50"]:hover {
            background: #202c3b !important;
        }

        .void-admin main input::placeholder,
        .void-admin main textarea::placeholder {
            color: #9aa8ba !important;
            opacity: 1;
        }

        .void-admin main input:not([type=checkbox]):not([type=radio]):not([type=file]),
        .void-admin main select,
        .void-admin main textarea {
            background-color: #101720;
            border-color: #405064;
        }

        .void-admin main input:focus,
        .void-admin main select:focus,
        .void-admin main textarea:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 3px rgb(59 130 246 / 24%);
        }

        .void-admin main table {
            width: 100%;
            color: #e5eaf1;
            background: #131923;
            border-color: #303b4b;
        }

        .void-admin main thead th {
            color: #edf1f7 !important;
            background: #1c2633;
        }

        .void-admin main tbody td {
            color: #dce2eb;
        }

        .void-admin main tbody tr {
            border-color: #2b3544;
        }

        .void-admin main tbody tr:hover {
            background: #1a2532;
        }

        .void-admin main a:focus-visible,
        .void-admin main button:focus-visible,
        .void-admin main input:focus-visible,
        .void-admin main select:focus-visible,
        .void-admin main textarea:focus-visible {
            outline: 2px solid #60a5fa;
            outline-offset: 2px;
        }

        .void-card {
            background: linear-gradient(145deg, #18212d 0%, #121720 100%) !important;
            border-color: #303b4b !important;
            box-shadow: 0 18px 40px rgb(0 0 0 / 18%);
        }

        .void-card>div:first-child {
            border-color: #303b4b !important;
        }

        .void-card h3 {
            color: #f4f6fa !important;
        }

        .void-stat-card {
            background: linear-gradient(145deg, #1a2431 0%, #111720 100%) !important;
            border-color: #354254 !important;
            box-shadow: 0 18px 40px rgb(0 0 0 / 20%);
        }

        .void-stat-card p:first-child {
            color: #c5ceda !important;
        }

        .void-stat-card p:last-child {
            color: #ffffff !important;
        }

        .void-page-heading {
            position: relative;
            border-bottom-color: #303b4b;
            padding-bottom: 1.35rem;
        }

        .void-page-heading:after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -1px;
            width: 6rem;
            height: 2px;
            background: linear-gradient(90deg, var(--void-amber), var(--void-blue));
            border-radius: 999px;
        }

        .void-page-heading h1,
        .void-page-heading h2 {
            color: #f8fafc !important;
        }

        .void-page-heading p:not(.void-page-eyebrow) {
            color: #c0cad7 !important;
        }

        .void-admin aside nav a {
            min-height: 2.75rem;
        }

        .void-admin aside nav a:not([aria-current=page]) {
            color: #c0cad7;
        }

        .void-admin aside nav a:not([aria-current=page]):hover {
            color: #ffffff;
        }

        .table-scroll-toolbar {
            color: #b7c1d0;
        }


    </style>

    @stack('styles')
</head>

<body class="void-admin h-full bg-neutral-950 text-neutral-100 antialiased">

    <div x-data="{ sidebarOpen: false }" class="min-h-screen flex">

        <div x-show="sidebarOpen" x-cloak x-transition.opacity.duration.200ms @click="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-black/70 backdrop-blur-sm lg:hidden" aria-hidden="true"></div>

        @include('layouts.partials.sidebar')

        <div class="flex-1 flex flex-col min-w-0 lg:pl-64">
            @include('layouts.partials.navbar')

            <main class="void-main flex-1 p-4 sm:p-6 lg:p-8">
                @if (session('success'))
                    <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
                @endif
                @if (session('error'))
                    <x-alert type="error" class="mb-6">{{ session('error') }}</x-alert>
                @endif
                @if (session()->has('relaciones_bloqueo'))
                    @include('layouts.partials.relaciones-bloqueo', [
                        'panel' => session('relaciones_bloqueo'),
                    ])
                @endif

                @yield('content')
            </main>

            @include('layouts.partials.footer')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js" defer></script>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Aplica controles a las tablas de todos los módulos, incluidas las tablas reutilizables.
            document.querySelectorAll('main table').forEach(function(table) {
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
                const updateControls = function() {
                    const maxScroll = area.scrollWidth - area.clientWidth;
                    left.disabled = area.scrollLeft <= 2;
                    right.disabled = area.scrollLeft >= maxScroll - 2;
                    toolbar.hidden = maxScroll <= 2;
                };

                left.addEventListener('click', function() {
                    area.scrollBy({
                        left: -Math.max(240, area.clientWidth * 0.75),
                        behavior: 'smooth'
                    });
                });
                right.addEventListener('click', function() {
                    area.scrollBy({
                        left: Math.max(240, area.clientWidth * 0.75),
                        behavior: 'smooth'
                    });
                });
                area.addEventListener('scroll', updateControls, {
                    passive: true
                });
                window.addEventListener('resize', updateControls);
                updateControls();
            });
        });
    </script>
</body>

</html>
