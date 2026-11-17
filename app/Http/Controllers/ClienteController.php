<?php
namespace App\Http\Controllers;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use App\Services\ClienteService;
use App\Services\RelacionesBloqueoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ClienteController extends Controller
{
    public function __construct(private ClienteService $clienteService,private RelacionesBloqueoService $relacionesBloqueoService) {}
    public function index() {
        $this->authorize('viewAny',Cliente::class);
        $tiendaId=(int)(Auth::user()?->id_tienda ?? 0);
        $clientes=$this->clienteService->listarTodo($tiendaId);
        return view('clientes.index',compact('clientes'));
    }
    public function create() {
        $this->authorize('create',Cliente::class);
        return view('clientes.crear');
    }
    public function store(StoreClienteRequest $request) {
        $this->authorize('create',Cliente::class);
        $this->clienteService->guardar($request->validated(),(int)(Auth::user()?->id_tienda ?? 0));
        return redirect()->route('cliente.index')->with('success','Cliente creado correctamente.');
    }
    public function edit(int $id) {
        $tiendaId=(int)(Auth::user()?->id_tienda ?? 0);
        $cliente=$this->clienteService->buscarPorId($id,$tiendaId);
        $this->authorize('update',$cliente);
        return view('clientes.editar',compact('cliente'));
    }
    public function update(int $cliente,UpdateClienteRequest $request) {
        $tiendaId=(int)(request()->user()?->id_tienda ?? 0);
        $clienteEncontrado=$this->clienteService->buscarPorId($cliente,$tiendaId);
        Gate::authorize('update',$clienteEncontrado);
        $this->clienteService->actualizar($cliente,$request->validated(),$tiendaId);
        return redirect()->route('cliente.index')->with('success','Cliente actualizado correctamente.');
    }
    public function destroy(int $id) {
        $tiendaId=(int)(Auth::user()?->id_tienda ?? 0);
        $cliente=$this->clienteService->buscarPorId($id,$tiendaId);
        $this->authorize('delete',$cliente);
        $bloqueos=$this->relacionesBloqueoService->paraCliente($id,$tiendaId);
        if ($bloqueos!==[]) {
            $this->relacionesBloqueoService->flash('No se puede eliminar este cliente','El cliente tiene facturas asociadas. Se conserva su registro para que el historial de ventas siga completo.',$bloqueos);
            return redirect()->route('cliente.index')->with('error','El cliente tiene facturas relacionadas. Revisa los registros indicados.');
        }
        if (!$this->clienteService->eliminar($id,$tiendaId)) {
            return redirect()->route('cliente.index')->with('error','No se pudo eliminar el cliente porque todavía tiene registros asociados.');
        }
        return redirect()->route('cliente.index')->with('success','Cliente eliminado correctamente.');
    }
}
