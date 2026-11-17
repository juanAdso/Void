@extends('layouts.app')

@section('title', 'Administrar empresa')
@section('page-title', 'Administrar empresa')

@section('content')
    <div class="mb-6">
        <a href="{{ route('superadmin.empresas.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">&larr; Volver a empresas</a>
        <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $tienda->nombre }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $tienda->correo ?: 'Sin correo de contacto' }} · {{ $tienda->telefono ?: 'Sin teléfono' }}</p>
            </div>
            <form method="POST" action="{{ route('superadmin.empresas.estado', $tienda) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="estado" value="{{ $tienda->estado ? 0 : 1 }}">
                <button type="submit" class="rounded-lg px-4 py-2 text-sm font-semibold text-white {{ $tienda->estado ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700' }}" onclick="return confirm('{{ $tienda->estado ? '¿Suspender esta empresa y bloquear el acceso de todas sus cuentas?' : '¿Activar esta empresa?' }}')">
                    {{ $tienda->estado ? 'Suspender empresa' : 'Activar empresa' }}
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Administradores', 'value' => $estadisticas['administradores']],
            ['label' => 'Empleados', 'value' => $estadisticas['empleados']],
            ['label' => 'Productos', 'value' => $estadisticas['productos']],
            ['label' => 'Clientes', 'value' => $estadisticas['clientes']],
        ] as $dato)
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">{{ $dato['label'] }}</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($dato['value']) }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-7 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
            <div class="border-b border-slate-200 p-5">
                <h2 class="text-lg font-semibold text-slate-900">Cuentas de esta empresa</h2>
                <p class="text-sm text-slate-500">Puedes activar o desactivar administradores y empleados sin borrar su historial.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Usuario</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Rol</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Estado</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-500">Acceso</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($tienda->usuarios as $usuario)
                            <tr>
                                <td class="px-4 py-4">
                                    <p class="font-semibold text-slate-800">{{ $usuario->nombre }} {{ $usuario->apellido }}</p>
                                    <p class="text-xs text-slate-500">{{ $usuario->correo }}</p>
                                    <p class="text-xs text-slate-400">{{ $usuario->documento }}</p>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-600">{{ ucfirst($usuario->rol) }}</td>
                                <td class="px-4 py-4">
                                    @if ($usuario->estado)
                                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Activa</span>
                                    @else
                                        <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">Inactiva</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <form method="POST" action="{{ route('superadmin.empresas.usuarios.estado', [$tienda, $usuario]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="estado" value="{{ $usuario->estado ? 0 : 1 }}">
                                        <button type="submit" class="rounded-lg border px-3 py-2 text-xs font-semibold {{ $usuario->estado ? 'border-red-200 text-red-700 hover:bg-red-50' : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50' }}">
                                            {{ $usuario->estado ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">Esta empresa todavía no tiene cuentas asociadas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Crear administrador</h2>
            <p class="mt-1 text-sm text-slate-500">La nueva cuenta quedará asociada exclusivamente a {{ $tienda->nombre }}.</p>

            <form method="POST" action="{{ route('superadmin.empresas.administradores.store', $tienda) }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label for="nombre" class="mb-1 block text-sm font-medium text-slate-700">Nombre</label>
                    <input id="nombre" name="nombre" value="{{ old('nombre') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    @error('nombre')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="apellido" class="mb-1 block text-sm font-medium text-slate-700">Apellido</label>
                    <input id="apellido" name="apellido" value="{{ old('apellido') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    @error('apellido')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="documento" class="mb-1 block text-sm font-medium text-slate-700">Documento</label>
                    <input id="documento" name="documento" value="{{ old('documento') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    @error('documento')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="correo" class="mb-1 block text-sm font-medium text-slate-700">Correo de acceso</label>
                    <input id="correo" type="email" name="correo" value="{{ old('correo') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    @error('correo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Contraseña inicial</label>
                    <input id="password" type="password" name="password" required minlength="8" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-700">Confirmar contraseña</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Crear cuenta de administrador</button>
            </form>
        </section>
    </div>
@endsection
