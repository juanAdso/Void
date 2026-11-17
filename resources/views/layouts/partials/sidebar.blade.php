@php
    $esSuperadministrador = auth()->check() && auth()->user()->hasRole('superadministrador');

    if ($esSuperadministrador) {
        $links = [
            [
                'label' => 'Centro de control',
                'route' => 'superadmin.dashboard',
                'active' => 'superadmin.dashboard',
                'icon' => 'home',
            ],
            [
                'label' => 'Empresas',
                'route' => 'superadmin.empresas.index',
                'active' => 'superadmin.empresas.*',
                'icon' => 'store',
            ],
        ];
    } else {
        $links = [
            [
                'label' => 'Inicio',
                'route' => 'dashboard.index',
                'active' => 'dashboard.*',
                'icon' => 'home',
                'can' => ['ver dashboard'],
            ],
            [
                'label' => 'Reportes e Inventario',
                'route' => 'reportes.index',
                'active' => 'reportes.*',
                'icon' => 'chart',
                'can' => ['ver dashboard'],
            ],
            [
                'label' => 'Tiendas',
                'route' => 'tiendas.index',
                'active' => 'tiendas.*',
                'icon' => 'store',
                'can' => ['ver tiendas'],
            ],
            [
                'label' => 'Clientes',
                'route' => 'cliente.index',
                'active' => 'cliente.*',
                'icon' => 'users',
                'can' => ['ver clientes'],
            ],
            [
                'label' => 'Categorías',
                'route' => 'categoria.index',
                'active' => 'categoria.*',
                'icon' => 'tag',
                'can' => ['ver categorias'],
            ],
            [
                'label' => 'Productos',
                'route' => 'productos.index',
                'active' => 'productos.*',
                'icon' => 'box',
                'can' => ['ver productos'],
            ],
            [
                'label' => 'Facturas',
                'route' => 'facturas.index',
                'active' => 'facturas.*',
                'icon' => 'chart',
                'can' => ['ver facturas'],
            ],
            [
                'label' => 'Detalle de Facturas',
                'route' => 'detalle_facturas.index',
                'active' => 'detalle_facturas.*',
                'icon' => 'cog',
                'can' => ['ver detallefacturas'],
            ],
            [
                'label' => 'Empleados',
                'route' => 'empleados.index',
                'active' => 'empleados.*',
                'icon' => 'users',
                'can' => ['gestionar seguridad'],
            ],
        ];
    }
@endphp

<aside x-cloak :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-40 w-64 border-r border-neutral-800 bg-neutral-950 text-slate-300 transform transition-transform duration-300 ease-out lg:translate-x-0"
    aria-label="Menú principal">
    <div class="h-full flex flex-col">
        <div class="flex items-center gap-3 h-16 px-6 border-b border-neutral-800">
            <div
                class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-amber-300 font-bold shadow-lg shadow-blue-950/40">
                Ø</div>
            <div>
                <span class="block text-amber-50 font-extrabold text-lg tracking-[0.18em]">VOID</span>
                <span class="block text-[10px] uppercase tracking-widest text-slate-500">
                    {{ $esSuperadministrador ? 'Administración global' : 'Panel de empresa' }}
                </span>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-6 space-y-1" aria-label="Navegación lateral">
            @foreach ($links as $link)
                @if (auth()->check() && (!isset($link['can']) || auth()->user()->canAny($link['can'])))
                    @php $active = request()->routeIs($link['active']); @endphp
                    <a href="{{ route($link['route']) }}" @if ($active) aria-current="page" @endif
                        class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ $active ? 'bg-blue-600/15 border border-blue-500/20 text-amber-300 shadow-inner shadow-blue-950/20' : 'text-slate-400 hover:bg-neutral-900 hover:text-amber-50 hover:translate-x-0.5' }}">
                        <span class="w-5 h-5 flex-shrink-0" aria-hidden="true">
                            @include('layouts.partials.icons', ['icon' => $link['icon']])
                        </span>
                        <span>{{ $link['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="px-3 py-4 border-t border-neutral-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-400 hover:bg-red-950/40 hover:text-red-300 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500"
                    aria-label="Cerrar sesión">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</aside>
