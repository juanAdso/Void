<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory, PerteneceATienda;

    protected $table = 'categorias';

    protected $fillable = [
        'id_tienda',
        'nombre',
        'descripcion',
        'estado',
    ];

    protected function casts(): array
    {
        return ['estado' => 'boolean'];
    }

    public function tienda()
    {
        return $this->belongsTo(Tienda::class, 'id_tienda', 'id');
    }

    public function productos()
    {
        return $this->hasMany(Productos::class, 'id_categoria', 'id');
    }
}
