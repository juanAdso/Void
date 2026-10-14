<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facturas extends Model
{

    use HasFactory;
    protected $table = 'facturas';

    protected $fillable = [
        'id_cliente',
        'fecha',
        'subtotal',
        'iva',
        'total',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(
            Cliente::class,
            'id_cliente',
            'id'
        );
    }

    public function detalle_facturas()
    {
        return $this->hasMany(
            DetalleFactura::class,
            'id_factura',
            'id'
        );
    }
}
