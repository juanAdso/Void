<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmpleadoRequest;
use App\Http\Requests\UpdateEmpleadoRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmpleadoController extends Controller
{
    public function index()
    {
        $tiendaId = auth()->user()->alcanceTiendaId();

        $consulta = User::query()->with('tienda')->where('rol', 'empleado');

        if ($tiendaId !== null) {
            $consulta->where('id_tienda', $tiendaId);
        }

        $empleados = $consulta->orderBy('nombre')->get();

        return view('empleados.index', compact('empleados'));
    }

    public function create()
    {
        abort_unless(auth()->user()->hasRole('administrador'), 403);

        return view('empleados.create');
    }

    public function store(StoreEmpleadoRequest $request)
    {
        $admin = auth()->user();
        abort_unless($admin->hasRole('administrador') && $admin->id_tienda, 403);

        $datos = $request->validated();
        $datos['id_tienda'] = $admin->id_tienda;
        $datos['rol'] = 'empleado';
        $datos['password'] = Hash::make($datos['password']);

        $empleado = User::create($datos);
        $empleado->assignRole('empleado');

        return redirect()->route('empleados.index')->with('success', 'Empleado creado y asignado a tu tienda exitosamente.');
    }

    public function edit(int $id)
    {
        $admin = auth()->user();
        abort_unless($admin->hasRole('administrador') && $admin->id_tienda, 403);

        $empleado = User::query()
            ->where('id_tienda', $admin->id_tienda)
            ->where('rol', 'empleado')
            ->findOrFail($id);

        return view('empleados.edit', compact('empleado'));
    }

    public function update(int $id, UpdateEmpleadoRequest $request)
    {
        $admin = auth()->user();
        abort_unless($admin->hasRole('administrador') && $admin->id_tienda, 403);

        $empleado = User::query()
            ->where('id_tienda', $admin->id_tienda)
            ->where('rol', 'empleado')
            ->findOrFail($id);

        $datos = $request->validated();

        if (! empty($datos['password'])) {
            $datos['password'] = Hash::make($datos['password']);
        } else {
            unset($datos['password']);
        }

        unset($datos['id_tienda'], $datos['rol']);
        $empleado->update($datos);

        return redirect()->route('empleados.index')->with('success', 'Información del empleado actualizada correctamente.');
    }

    public function destroy(int $id)
    {
        $admin = auth()->user();
        abort_unless($admin->hasRole('administrador') && $admin->id_tienda, 403);

        $empleado = User::query()
            ->where('id_tienda', $admin->id_tienda)
            ->where('rol', 'empleado')
            ->findOrFail($id);

        if ($empleado->facturas()->exists()) {
            $empleado->update(['estado' => false]);

            return redirect()->route('empleados.index')->with('success', 'El empleado posee facturas emitidas, por lo que fue desactivado del sistema.');
        }

        $empleado->delete();

        return redirect()->route('empleados.index')->with('success', 'Empleado eliminado correctamente.');
    }
}
