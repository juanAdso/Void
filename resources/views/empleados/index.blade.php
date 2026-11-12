@extends('layouts.app')

@section('title', 'Gestión de Empleados')
@section('page-title', 'Gestión de Empleados')

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ auth()->user()->esSuperadministrador() ? 'Empleados de todas las tiendas' : 'Empleados de tu tienda' }}</h1>
            <p class="text-sm text-slate-500">{{ auth()->user()->esSuperadministrador() ? 'Consulta global de empleados de las empresas registradas.' : 'Administra los accesos y colaboradores asignados a tu tienda.' }}</p>
        </div>
        <x-button variant="primary" href="{{ route('empleados.create') }}">
            + Registrar Empleado
        </x-button>
    </div>

    <x-card :padding="false">
        <x-table :headers="auth()->user()->esSuperadministrador() ? ['Empleado', 'Tienda', 'Documento', 'Correo', 'Estado'] : ['Empleado', 'Documento', 'Correo', 'Estado', 'Acciones']">
            @forelse($empleados as $empleado)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr($empleado->nombre, 0, 1)) }}
                            </div>
                            <div>
                                <span class="font-medium text-slate-800 block">{{ $empleado->nombre }} {{ $empleado->apellido }}</span>
                                <span class="text-xs text-slate-400">Rol: Empleado</span>
                            </div>
                        </div>
                    </td>
                    @if(auth()->user()->esSuperadministrador())
                    <td class="px-5 py-3.5 text-slate-600 text-sm">{{ $empleado->tienda?->nombre ?? 'Sin tienda' }}</td>
                    @endif
                    <td class="px-5 py-3.5 text-slate-600 text-sm font-mono">
                        {{ $empleado->documento }}
                    </td>
                    <td class="px-5 py-3.5 text-slate-600 text-sm">
                        {{ $empleado->correo }}
                    </td>
                    <td class="px-5 py-3.5">
                        @if($empleado->estado)
                            <x-badge status="activo" />
                        @else
                            <x-badge status="inactivo" />
                        @endif
                    </td>
                    @unless(auth()->user()->esSuperadministrador())
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('empleados.edit', $empleado->id) }}" class="text-sm font-medium text-primary-600 hover:text-primary-800">Editar</a>
                            <span class="text-slate-300">|</span>
                            <form action="{{ route('empleados.destroy', $empleado->id) }}" method="POST" onsubmit="return confirm('¿Confirmas que deseas eliminar o desactivar a este empleado?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">Eliminar</button>
                            </form>
                        </div>
                    </td>
                    @endunless
                </tr>
            @empty
                <tr>
                    <td colspan="{{ auth()->user()->esSuperadministrador() ? 5 : 5 }}" class="px-5 py-12 text-center text-slate-400">
                        No hay empleados registrados.
                    </td>
                </tr>
            @endforelse
        </x-table>
    </x-card>
@endsection
