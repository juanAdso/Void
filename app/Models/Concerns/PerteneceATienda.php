<?php

namespace App\Models\Concerns;

use App\Models\Scopes\TiendaScope;

trait PerteneceATienda
{
    protected static function bootPerteneceATienda(): void
    {
        static::addGlobalScope(new TiendaScope);
    }
}
