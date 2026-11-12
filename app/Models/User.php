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
        'id_tienda',
        'estado',
    ];

    protected $hidden = ['password', 'remember_token'];

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

    /**
     * El único superadministrador es la cuenta cuyo correo está autorizado
     * explícitamente en VOID_SUPERADMIN_EMAIL y tiene el rol global asignado.
     */
    public function esSuperadministrador(): bool
    {
        $correoAutorizado = mb_strtolower(trim((string) config('void.superadmin_email')));
        $correoUsuario = mb_strtolower(trim((string) $this->correo));

        if (
            ! $this->exists
            || ! $this->estaActivo()
            || $this->id_tienda !== null
            || $correoAutorizado === ''
            || $correoUsuario === ''
            || ! hash_equals($correoAutorizado, $correoUsuario)
        ) {
            return false;
        }

        return $this->hasRole('superadministrador');
    }

    /**
     * Devuelve null exclusivamente para el superadministrador global.
     * Las cuentas normales sin tienda quedan bloqueadas, nunca globales.
     */
    public function alcanceTiendaId(): ?int
    {
        abort_unless($this->estaActivo(), 403, 'La cuenta está inactiva.');

        if ($this->esSuperadministrador()) {
            return null;
        }

        abort_if(
            ! $this->id_tienda,
            403,
            'La cuenta no tiene una tienda asignada. No se permite acceso global.'
        );

        return (int) $this->id_tienda;
    }

    /**
     * Operaciones de creación que necesitan obligatoriamente una tienda destino.
     */
    public function requiereTiendaId(): int
    {
        $tiendaId = $this->alcanceTiendaId();

        abort_if(
            $tiendaId === null,
            403,
            'Selecciona una tienda destino para realizar esta operación.'
        );

        return $tiendaId;
    }

    public function tienda()
    {
        return $this->belongsTo(Tienda::class, 'id_tienda');
    }

    public function facturas()
    {
        return $this->hasMany(Facturas::class, 'id_usuario');
    }
}
