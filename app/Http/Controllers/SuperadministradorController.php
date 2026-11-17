<?php

namespace App\Http\Controllers;

use App\Models\Tienda;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SuperadministradorController extends Controller
{
    public function dashboard()
    {
        $estadisticas = [
            'empresas' => Tienda::count(),
            'empresas_activas' => Tienda::where('estado', true)->count(),
            'administradores' => User::where('rol', 'administrador')->count(),
            'empleados' => User::where('rol', 'empleado')->count(),
        ];

        $empresas = $this->consultaEmpresas()
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('superadmin.dashboard', compact('estadisticas', 'empresas'));
    }

    public function empresas()
    {
        $empresas = $this->consultaEmpresas()
            ->orderBy('nombre')
            ->paginate(15);

        return view('superadmin.empresas.index', compact('empresas'));
    }

    public function showEmpresa(Tienda $tienda)
    {
        $tienda->load([
            'usuarios' => fn ($query) => $query
                ->whereIn('rol', ['administrador', 'empleado'])
                ->orderBy('rol')
                ->orderBy('nombre'),
        ]);

        $estadisticas = [
            'productos' => $tienda->productos()->count(),
            'clientes' => $tienda->clientes()->count(),
            'administradores' => $tienda->usuarios->where('rol', 'administrador')->count(),
            'empleados' => $tienda->usuarios->where('rol', 'empleado')->count(),
        ];

        return view('superadmin.empresas.show', compact('tienda', 'estadisticas'));
    }

    public function cambiarEstadoEmpresa(Request $request, Tienda $tienda)
    {
        $datos = $request->validate([
            'estado' => ['required', 'boolean'],
        ]);

        $tienda->update(['estado' => (bool) $datos['estado']]);

        $mensaje = $tienda->estado
            ? 'La empresa fue activada. Sus cuentas habilitadas pueden volver a ingresar.'
            : 'La empresa fue suspendida. Ninguna de sus cuentas puede acceder al panel de la tienda.';

        return back()->with('success', $mensaje);
    }

    public function storeAdministrador(Request $request, Tienda $tienda)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'documento' => ['required', 'string', 'max:20', 'unique:usuarios,documento'],
            'correo' => ['required', 'email', 'max:150', 'unique:usuarios,correo'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        DB::transaction(function () use ($datos, $tienda) {
            $administrador = User::create([
                'nombre' => $datos['nombre'],
                'apellido' => $datos['apellido'],
                'documento' => $datos['documento'],
                'correo' => $datos['correo'],
                'password' => Hash::make($datos['password']),
                'rol' => 'administrador',
                'id_tienda' => $tienda->id,
                'estado' => true,
            ]);

            $administrador->assignRole('administrador');
        });

        return redirect()
            ->route('superadmin.empresas.show', $tienda)
            ->with('success', 'La cuenta de administrador fue creada y asociada a esta empresa.');
    }

    public function cambiarEstadoUsuario(Request $request, Tienda $tienda, User $usuario)
    {
        abort_unless(
            (int) $usuario->id_tienda === (int) $tienda->id
                && in_array($usuario->rol, ['administrador', 'empleado'], true)
                && $usuario->hasAnyRole(['administrador', 'empleado']),
            404
        );

        $datos = $request->validate([
            'estado' => ['required', 'boolean'],
        ]);

        $nuevoEstado = (bool) $datos['estado'];

        if (! $nuevoEstado && $usuario->rol === 'administrador' && $usuario->estado) {
            $administradoresActivos = User::query()
                ->where('id_tienda', $tienda->id)
                ->where('rol', 'administrador')
                ->where('estado', true)
                ->count();

            if ($administradoresActivos <= 1) {
                throw ValidationException::withMessages([
                    'estado' => 'No puedes desactivar al último administrador activo de la empresa. Crea o activa otro administrador primero.',
                ]);
            }
        }

        $usuario->update(['estado' => $nuevoEstado]);

        return back()->with(
            'success',
            $nuevoEstado ? 'La cuenta fue activada.' : 'La cuenta fue desactivada sin eliminar su historial.'
        );
    }

    private function consultaEmpresas()
    {
        return Tienda::query()->withCount([
            'usuarios as administradores_count' => fn ($query) => $query->where('rol', 'administrador'),
            'usuarios as empleados_count' => fn ($query) => $query->where('rol', 'empleado'),
            'productos',
            'clientes',
        ]);
    }
}
