<?php

namespace App\Models;

use App\Models\Concerns\TieneRolesActivos;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable, TieneRolesActivos;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'apellido',
        'documento',
        'correo',
        'password',
        'rol',
        'estado',
    ];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'estado' => 'boolean',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }
}
