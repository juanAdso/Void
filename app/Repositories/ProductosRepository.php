<?php
namespace App\Repositories;
use App\Models\Categoria;
use App\Models\DetalleFactura;
use App\Models\MovimientoInventario;
use App\Models\Productos;
use App\Repositories\Interfaces\ProductoRepositoryInterface;

class ProductosRepository implements ProductoRepositoryInterface
{
    public function listarTodo(int $tiendaId) {
        return Productos::query()->with('categoria')->where('id_tienda',$tiendaId)->orderBy('id')->get();
    }
    public function guardar(array $datos,int $tiendaId) {
        Categoria::query()->where('id_tienda',$tiendaId)->findOrFail($datos['id_categoria']);
        $datos['id_tienda']=$tiendaId;
        return Productos::create($datos);
    }
    public function eliminar(int $id,int $tiendaId) {
        $producto=Productos::query()->where('id_tienda',$tiendaId)->findOrFail($id);
        $tieneDetalles=DetalleFactura::query()->where('id_producto',$producto->id)
            ->whereHas('factura.usuario',fn($q)=>$q->where('id_tienda',$tiendaId))->exists();
        $tieneMovimientos=MovimientoInventario::query()->where('id_producto',$producto->id)->where('id_tienda',$tiendaId)->exists();
        if ($tieneDetalles || $tieneMovimientos) return false;
        return $producto->delete();
    }
    public function buscarPorId(int $id,int $tiendaId) {
        return Productos::query()->with('categoria')->where('id_tienda',$tiendaId)->findOrFail($id);
    }
    public function actualizar(int $id,array $datos,int $tiendaId) {
        if (isset($datos['id_categoria'])) Categoria::query()->where('id_tienda',$tiendaId)->findOrFail($datos['id_categoria']);
        $producto=Productos::query()->where('id_tienda',$tiendaId)->findOrFail($id);
        unset($datos['id_tienda']);$producto->update($datos);
        return $producto->refresh();
    }
}
