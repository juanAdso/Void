<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tienda extends Model

{
    use HasFactory;

    protected $table = 'tiendas';

    protected $primaryKey = 'id_tienda';

    protected $fillable = [
        'nombre',
        'descripcion',
        'logo',
        'direccion',
        'telefono',
        'correo',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }
}

