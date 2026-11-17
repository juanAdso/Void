<header class="sticky top-0 z-20 border-b border-neutral-800 bg-neutral-950/95 text-amber-50 shadow-lg shadow-black/10 backdrop-blur-xl">
    <div class="h-16 px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">

        <div class="flex items-center gap-4 min-w-0">

            <button
                @click="sidebarOpen = !sidebarOpen"
                type="button"
                class="lg:hidden p-2 rounded-lg text-slate-300 hover:bg-neutral-800 hover:text-amber-50 focus:outline-none focus:ring-2 focus:ring-primary-500"
                aria-label="Abrir menú lateral"
            >
                <span class="w-6 h-6 block">@include('layouts.partials.icons', ['icon' => 'menu'])</span>
            </button>

            <h1 class="text-lg sm:text-xl font-semibold text-amber-50 truncate">
                @yield('page-title', 'Dashboard')
            </h1>
        </div>

        <div class="flex items-center gap-2 sm:gap-4">


            @if (!auth()->user()->hasRole('superadministrador'))
            <form method="GET" action="{{ route('busqueda.global') }}" class="relative hidden md:block" role="search">
                <label for="global-search" class="sr-only">Buscar</label>
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500 w-4 h-4 pointer-events-none">
                    @include('layouts.partials.icons', ['icon' => 'search'])
                </span>
                <input
                    id="global-search"
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Buscar en Void..."
                    class="w-56 lg:w-72 pl-9 pr-3 py-2 text-sm rounded-lg border border-neutral-700 bg-neutral-900 text-slate-100 placeholder:text-slate-500 focus:bg-neutral-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                >
            </form>
            @endif


            <div x-data="{ open: false }" class="relative">
                <button
                    @click="open = !open"
                    @click.outside="open = false"
                    type="button"
                    class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-xl border border-transparent hover:border-neutral-800 hover:bg-neutral-900 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    aria-haspopup="true"
                    :aria-expanded="open.toString()"
                >
                    <img
                        class="w-8 h-8 rounded-full object-cover ring-1 ring-blue-500/40"
                        src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name ?? 'Admin') }}"
                        alt="Avatar de {{ auth()->user()->name ?? 'Usuario' }}"
                    >
                    <span class="hidden sm:block text-right">
                        <span class="block text-sm font-medium text-slate-200">
                            {{ trim((auth()->user()->nombre ?? '') . ' ' . (auth()->user()->apellido ?? '')) ?: (auth()->user()->name ?? 'Usuario') }}
                        </span>
                        <span class="block text-[10px] uppercase tracking-wider text-slate-500">
                            {{ auth()->user()->rol === 'superadministrador' ? 'Superadministrador VOID' : ucfirst(auth()->user()->rol ?? 'Cuenta') }}
                        </span>
                    </span>
                    <span class="w-4 h-4 text-slate-500 hidden sm:block">@include('layouts.partials.icons', ['icon' => 'chevron-down'])</span>
                </button>

                <div
                    x-show="open" x-cloak
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                    class="absolute right-0 mt-2 w-52 origin-top-right bg-neutral-900 rounded-xl shadow-2xl shadow-black/40 border border-neutral-800 overflow-hidden py-1"
                >
                    <form method="POST" action="{{ Route::has('logout') ? route('logout') : '#' }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-red-950">
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
