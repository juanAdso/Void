<?php
namespace App\Http\Controllers;
use App\Http\Requests\StoreFacturasRequest;
use App\Http\Requests\UpdateFacturasRequest;
use App\Services\ClienteService;
use App\Services\FacturasService;
use App\Services\RelacionesBloqueoService;

class FacturasController extends Controller
{
    public function __construct(private FacturasService $facturasService,private ClienteService $clientesService,private RelacionesBloqueoService $relacionesBloqueoService) {}
    public function index() {
        $tiendaId=(int)auth()->user()->id_tienda;
        $facturas=$this->facturasService->listarTodo($tiendaId);
        return view('facturas.index',compact('facturas'));
    }
    public function create() {
        $tiendaId=(int)auth()->user()->id_tienda;
        $clientes=$this->clientesService->listarTodo($tiendaId);
        return view('facturas.create',compact('clientes'));
    }
    public function store(StoreFacturasRequest $request) {
        $tiendaId=(int)auth()->user()->id_tienda;
        $datos=$request->validated();
        $datos['id_usuario']=auth()->id();
        $datos['subtotal']=0;$datos['iva']=0;$datos['total']=0;
        $this->facturasService->guardar($datos,$tiendaId);
        return redirect()->route('facturas.index')->with('success','Factura creada. Ahora agrega sus productos desde Detalle de facturas.');
    }
    public function edit(int $id) {
        $tiendaId=(int)auth()->user()->id_tienda;
        $facturas=$this->facturasService->buscarPorId($id,$tiendaId);
        $clientes=$this->clientesService->listarTodo($tiendaId);
        return view('facturas.edit',compact('facturas','clientes'));
    }
    public function update(int $id,UpdateFacturasRequest $request) {
        $tiendaId=(int)auth()->user()->id_tienda;
        $datos=$request->validated();
        unset($datos['id_usuario'],$datos['id_tienda']);
        $this->facturasService->actualizar($id,$datos,$tiendaId);
        return redirect()->route('facturas.index')->with('success','Factura actualizada correctamente.');
    }
    public function destroy(int $id) {
        $tiendaId=(int)auth()->user()->id_tienda;
        $bloqueos=$this->relacionesBloqueoService->paraFactura($id,$tiendaId);
        if ($bloqueos!==[]) {
            $this->relacionesBloqueoService->flash('No se puede eliminar esta factura','Revisa los detalles asociados y el estado de la factura. Los registros comerciales deben conservarse cuando corresponda.',$bloqueos);
            return redirect()->route('facturas.index')->with('error','La factura tiene detalles asociados o su estado no permite eliminarla.');
        }
        if (!$this->facturasService->eliminar($id,$tiendaId)) {
            return redirect()->route('facturas.index')->with('error','No se pudo eliminar la factura. Verifica que esté pendiente y no tenga detalles asociados.');
        }
        return redirect()->route('facturas.index')->with('success','Factura eliminada correctamente.');
    }
}
