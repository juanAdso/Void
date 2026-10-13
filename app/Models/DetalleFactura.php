<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleFactura extends Model
{
    /** @use HasFactory<\Database\Factories\DetalleFacturaFactory> */
    use HasFactory;
    
    protected $table = 'detalle_factura';
    protected $fillable = [
        'id_factura',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    public function factura()
    {
        return $this->belongsTo(Facturas::class, 'id');
    }

    public function producto()
    {
        return $this->belongsTo(Productos::class, 'id');
    }
}
