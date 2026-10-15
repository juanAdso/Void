<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Facturas;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $fillable = [
        'nombre',
        'apellido',
        'documento',
        'telefono',
        'correo',
        'direccion',
    ];
    
    public function facturas()
    {
        return $this->hasMany(Facturas::class, 'id');
    }
}
