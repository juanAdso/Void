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

    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo) {
            return null;
        }

        if (preg_match('~^(https?:)?//~i', $this->logo) || str_starts_with($this->logo, '/')) {
            return $this->logo;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->logo);
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
