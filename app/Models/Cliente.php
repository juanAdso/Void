<?php

namespace App\Models;

use App\Models\Concerns\PerteneceATienda;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory, PerteneceATienda;

    protected $table = 'clientes';

    protected $fillable = [
        'id_tienda',
        'nombre',
        'apellido',
        'documento',
        'telefono',
        'correo',
        'direccion',
    ];

    public function tienda()
    {
        return $this->belongsTo(Tienda::class, 'id_tienda', 'id');
    }

    public function facturas()
    {
        return $this->hasMany(Facturas::class, 'id_cliente', 'id');
    }
}
