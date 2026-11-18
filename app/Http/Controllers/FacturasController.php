<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacturasRequest;
use App\Http\Requests\UpdateFacturasRequest;
use App\Services\ClienteService;
use App\Services\DetalleFacturaService;
use App\Services\FacturasService;
use App\Services\ProductosService;
use App\Services\RelacionesBloqueoService;
use Illuminate\Support\Facades\DB;

class FacturasController extends Controller
{
    public function __construct(
        private FacturasService $facturasService,
        private ClienteService $clientesService,
        private ProductosService $productosService,
        private DetalleFacturaService $detalleFacturaService,
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
        $productos = $this->productosService->listarTodo($tiendaId)
            ->where('estado', true)
            ->values();

        return view('facturas.create', compact('clientes', 'productos'));
    }

    public function store(StoreFacturasRequest $request)
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $datos = $request->validated();
        $detalles = $datos['detalles'] ?? [];
        unset($datos['detalles']);

        $datos['id_usuario'] = auth()->id();
        $datos['subtotal'] = 0;
        $datos['iva'] = 0;
        $datos['total'] = 0;

        $factura = DB::transaction(function () use ($datos, $detalles, $tiendaId) {
            $factura = $this->facturasService->guardar($datos, $tiendaId);

            foreach ($detalles as $detalle) {
                $detalle['id_factura'] = $factura->id;
                $this->detalleFacturaService->guardar($detalle, $tiendaId);
            }

            return $this->facturasService->buscarPorId((int) $factura->id, $tiendaId);
        });

        return redirect()->route('facturas.show', $factura->id)
            ->with('success', 'Factura creada correctamente. Sus productos, inventario y totales quedaron actualizados.');
    }

    public function show(int $id)
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $factura = $this->facturasService->buscarPorId($id, $tiendaId);
        $factura->load('usuario');

        $usuario = auth()->user();
        $esPendiente = $factura->estado === 'pendiente';
        $esAnulada = $factura->estado === 'anulada';
        $puedeEditarFactura = ! $esAnulada && $usuario->tienePermisoActivo('editar facturas');
        $puedeEditarDetalle = $esPendiente && $usuario->tienePermisoActivo('editar detallefacturas');
        $puedeEliminarDetalle = $esPendiente && $usuario->tienePermisoActivo('eliminar detallefacturas');
        $puedeAgregarDetalle = $esPendiente && $usuario->tienePermisoActivo('crear detallefacturas');
        $mostrarAccionesDetalle = $puedeEditarDetalle || $puedeEliminarDetalle;
        $clientes = $puedeEditarFactura ? $this->clientesService->listarTodo($tiendaId) : collect();
        $productos = collect();

        if ($puedeAgregarDetalle || $puedeEditarDetalle) {
            $productos = $this->productosService->listarTodo($tiendaId)
                ->where('estado', true)
                ->values();
        }

        return view('facturas.show', compact(
            'factura',
            'clientes',
            'productos',
            'puedeEditarFactura',
            'mostrarAccionesDetalle',
            'puedeEditarDetalle',
            'puedeEliminarDetalle',
            'puedeAgregarDetalle'
        ));
    }

    public function edit(int $id)
    {
        $this->facturasService->buscarPorId($id, (int) auth()->user()->id_tienda);

        return redirect()->route('facturas.show', $id);
    }

    public function update(int $id, UpdateFacturasRequest $request)
    {
        $tiendaId = (int) auth()->user()->id_tienda;
        $datos = $request->validated();
        unset($datos['id_usuario'], $datos['id_tienda']);
        $factura = $this->facturasService->actualizar($id, $datos, $tiendaId);

        return redirect()->route('facturas.show', $id)
            ->with('success', $factura->estado === 'anulada'
                ? 'Factura anulada correctamente. El inventario fue restituido.'
                : 'Datos de la factura actualizados correctamente.');
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
