<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Role tenant-aware. id_tienda=null is reserved for the single global role.
 */
class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'id_tienda',
    ];
}
