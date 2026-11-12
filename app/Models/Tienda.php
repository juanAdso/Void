<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tienda extends Model
{
    use HasFactory;

    protected $table = 'tiendas';

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
        return ['estado' => 'boolean'];
    }

    public function productos()
    {
        return $this->hasMany(Productos::class, 'id_tienda', 'id');
    }

    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_tienda', 'id');
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'id_tienda', 'id');
    }

    public function categorias()
    {
        return $this->hasMany(Categoria::class, 'id_tienda', 'id');
    }

    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class, 'id_tienda', 'id');
    }
}
