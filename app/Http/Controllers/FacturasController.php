<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacturasRequest;
use App\Http\Requests\UpdateFacturasRequest;
use App\Services\ClienteService;
use App\Services\FacturasService;
use App\Services\ProductosService;
use App\Services\RelacionesBloqueoService;

class FacturasController extends Controller
{
    public function __construct(
        private FacturasService $facturasService,
        private ClienteService $clientesService,
        private ProductosService $productosService,
        private RelacionesBloqueoService $relacionesBloqueoService
    ) {}

    public function index()
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $facturas = $this->facturasService->listarTodo($tiendaId);

        return view('facturas.index', compact('facturas'));
    }

    public function create()
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $clientes = $this->clientesService->listarTodo($tiendaId);

        return view('facturas.create', compact('clientes'));
    }

    public function store(StoreFacturasRequest $request)
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $datos = $request->validated();
        $datos['id_usuario'] = auth()->id();
        $datos['subtotal'] = 0;
        $datos['iva'] = 0;
        $datos['total'] = 0;
        $factura = $this->facturasService->guardar($datos, $tiendaId);

        return redirect()->route('facturas.show', $factura->id)
            ->with('success', 'Factura creada. Ahora puedes agregar sus productos desde esta misma pantalla.');
    }

    /**
     * Muestra la factura y determina las acciones permitidas según su estado y los permisos activos.
     *
     * Solo las facturas pendientes permiten modificar detalles o afectar las existencias.
     */
    public function show(int $id)
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $factura = $this->facturasService->buscarPorId($id, $tiendaId);
        $factura->load('usuario');

        $usuario = auth()->user();
        $esPendiente = $factura->estado === 'pendiente';
        $puedeEditarDetalle = $esPendiente && $usuario->tienePermisoActivo('editar detallefacturas');
        $puedeEliminarDetalle = $esPendiente && $usuario->tienePermisoActivo('eliminar detallefacturas');
        $puedeAgregarDetalle = $esPendiente && $usuario->tienePermisoActivo('crear detallefacturas');
        $mostrarAccionesDetalle = $puedeEditarDetalle || $puedeEliminarDetalle;
        $productos = collect();

        if ($puedeAgregarDetalle || $puedeEditarDetalle) {
            $productos = $this->productosService->listarTodo($tiendaId)
                ->where('estado', true)
                ->values();
        }

        return view('facturas.show', compact(
            'factura',
            'productos',
            'mostrarAccionesDetalle',
            'puedeEditarDetalle',
            'puedeEliminarDetalle',
            'puedeAgregarDetalle'
        ));
    }

    public function edit(int $id)
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $facturas = $this->facturasService->buscarPorId($id, $tiendaId);

        if ($facturas->estado === 'anulada') {
            return redirect()->route('facturas.show', $id)
                ->with('error', 'Esta factura está anulada y se conserva como parte del historial. No se puede editar.');
        }

        $usuario = auth()->user();
        $esPendiente = $facturas->estado === 'pendiente';
        $puedeEditarDetalle = $esPendiente && $usuario->tienePermisoActivo('editar detallefacturas');
        $puedeEliminarDetalle = $esPendiente && $usuario->tienePermisoActivo('eliminar detallefacturas');
        $puedeAgregarDetalle = $esPendiente && $usuario->tienePermisoActivo('crear detallefacturas');
        $mostrarAccionesDetalle = $puedeEditarDetalle || $puedeEliminarDetalle;
        $clientes = $this->clientesService->listarTodo($tiendaId);
        $productos = collect();

        if ($puedeAgregarDetalle || $puedeEditarDetalle) {
            $productos = $this->productosService->listarTodo($tiendaId)
                ->where('estado', true)
                ->values();
        }

        return view('facturas.edit', compact(
            'facturas',
            'clientes',
            'productos',
            'mostrarAccionesDetalle',
            'puedeEditarDetalle',
            'puedeEliminarDetalle',
            'puedeAgregarDetalle'
        ));
    }

    public function update(int $id, UpdateFacturasRequest $request)
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $datos = $request->validated();
        unset($datos['id_usuario'], $datos['id_tienda']);
        $this->facturasService->actualizar($id, $datos, $tiendaId);

        return redirect()->route('facturas.edit', $id)
            ->with('success', 'Factura actualizada correctamente.');
    }

    public function destroy(int $id)
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $factura = $this->facturasService->buscarPorId($id, $tiendaId);

        if ($factura->estado !== 'pendiente') {
            $bloqueos = $this->relacionesBloqueoService->paraFactura($id, $tiendaId);
            $this->relacionesBloqueoService->flash(
                'No se puede eliminar esta factura',
                'Solo se pueden eliminar facturas pendientes. Las facturas pagadas o anuladas deben conservarse como parte del historial comercial.',
                $bloqueos
            );

            return redirect()->route('facturas.show', $id)
                ->with('error', 'No se puede eliminar una factura pagada o anulada.');
        }

        if (! $this->facturasService->eliminar($id, $tiendaId)) {
            return redirect()->route('facturas.show', $id)
                ->with('error', 'No se pudo eliminar la factura. Verifica su estado y los productos asociados.');
        }

        return redirect()->route('facturas.index')
            ->with('success', 'Factura eliminada correctamente. El inventario fue restituido y los movimientos quedaron registrados.');
    }
}
