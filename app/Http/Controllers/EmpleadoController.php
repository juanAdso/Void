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
        $usuario = auth()->user();
        $tiendaId = $usuario?->id_tienda;

        $empleados = User::query()
            ->where('rol', 'empleado')
            ->when($tiendaId, fn ($q) => $q->where('id_tienda', $tiendaId))
            ->orderBy('nombre')
            ->get();

        return view('empleados.index', compact('empleados'));
    }

    public function create()
    {
        return view('empleados.create');
    }

    public function store(StoreEmpleadoRequest $request)
    {
        $admin = auth()->user();
        $tiendaId = $admin?->id_tienda;

        if (!$tiendaId) {
            abort(403, 'Debes tener una tienda asignada para registrar empleados.');
        }

        $datos = $request->validated();
        $datos['id_tienda'] = $tiendaId;
        $datos['rol'] = 'empleado';
        $datos['password'] = Hash::make($datos['password']);

        $empleado = User::create($datos);
        $empleado->assignRole('empleado');

        return redirect()->route('empleados.index')->with('success', 'Empleado creado y asignado a tu tienda exitosamente.');
    }

    public function edit(int $id)
    {
        $admin = auth()->user();
        $empleado = User::query()
            ->where('id', $id)
            ->where('rol', 'empleado')
            ->firstOrFail();

        if ($admin->id_tienda && (int) $empleado->id_tienda !== (int) $admin->id_tienda) {
            abort(403, 'No tienes autorización para editar empleados de otra tienda.');
        }

        return view('empleados.edit', compact('empleado'));
    }

    public function update(int $id, UpdateEmpleadoRequest $request)
    {
        $admin = auth()->user();
        $empleado = User::query()
            ->where('id', $id)
            ->where('rol', 'empleado')
            ->firstOrFail();

        if ($admin->id_tienda && (int) $empleado->id_tienda !== (int) $admin->id_tienda) {
            abort(403, 'No tienes autorización para modificar empleados de otra tienda.');
        }

        $datos = $request->validated();

        if (!empty($datos['password'])) {
            $datos['password'] = Hash::make($datos['password']);
        } else {
            unset($datos['password']);
        }

        unset($datos['id_tienda']);
        unset($datos['rol']);

        $empleado->update($datos);

        return redirect()->route('empleados.index')->with('success', 'Información del empleado actualizada correctamente.');
    }

    public function destroy(int $id)
    {
        $admin = auth()->user();
        $empleado = User::query()
            ->where('id', $id)
            ->where('rol', 'empleado')
            ->firstOrFail();

        if ($admin->id_tienda && (int) $empleado->id_tienda !== (int) $admin->id_tienda) {
            abort(403, 'No tienes autorización para eliminar empleados de otra tienda.');
        }

        if ($empleado->facturas()->exists()) {
            $empleado->update(['estado' => false]);
            return redirect()->route('empleados.index')->with('success', 'El empleado posee facturas emitidas, por lo que fue desactivado del sistema.');
        }

        $empleado->delete();

        return redirect()->route('empleados.index')->with('success', 'Empleado eliminado correctamente.');
    }
}
