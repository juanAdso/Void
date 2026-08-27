<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';

    protected $primaryKey = 'id_categorias';

    protected $fillable = [
        'nombre_categoria',
        'descripcion',
        'estado',
    ];

}
