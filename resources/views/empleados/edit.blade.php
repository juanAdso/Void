@extends('layouts.app')

@section('title', 'Editar Empleado')
@section('page-title', 'Editar Empleado')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('empleados.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-800">&larr; Volver al listado</a>
            <h1 class="mt-2 text-2xl font-bold text-slate-800">Editar Empleado: {{ $empleado->nombre }} {{ $empleado->apellido }}</h1>
            <p class="text-sm text-slate-500">Actualiza los datos o cambia el estado de acceso de este colaborador.</p>
        </div>

        <x-card>
            <form action="{{ route('empleados.update', $empleado->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-slate-700 mb-1">Nombre *</label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $empleado->nombre) }}" required
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        @error('nombre') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="apellido" class="block text-sm font-medium text-slate-700 mb-1">Apellido *</label>
                        <input type="text" name="apellido" id="apellido" value="{{ old('apellido', $empleado->apellido) }}" required
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        @error('apellido') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="documento" class="block text-sm font-medium text-slate-700 mb-1">Documento de identidad *</label>
                        <input type="text" name="documento" id="documento" value="{{ old('documento', $empleado->documento) }}" required
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        @error('documento') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="correo" class="block text-sm font-medium text-slate-700 mb-1">Correo electrónico *</label>
                        <input type="email" name="correo" id="correo" value="{{ old('correo', $empleado->correo) }}" required
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        @error('correo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Nueva Contraseña (opcional)</label>
                        <input type="password" name="password" id="password" minlength="6" placeholder="Dejar en blanco para no cambiar"
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="estado" class="block text-sm font-medium text-slate-700 mb-1">Estado de la cuenta *</label>
                        <select name="estado" id="estado" required
                                class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                            <option value="1" @selected(old('estado', $empleado->estado ? '1' : '0') == '1')>Activo (Puede acceder)</option>
                            <option value="0" @selected(old('estado', $empleado->estado ? '1' : '0') == '0')>Inactivo (Bloqueado)</option>
                        </select>
                        @error('estado') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <x-button variant="secondary" href="{{ route('empleados.index') }}">Cancelar</x-button>
                    <x-button variant="primary" type="submit">Actualizar Empleado</x-button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
