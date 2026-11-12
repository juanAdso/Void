<?php

return [
    // Debe configurarse fuera del repositorio en el entorno de producción.
    // La cuenta solo obtiene acceso global si además tiene el rol global asignado.
    'superadmin_email' => env('VOID_SUPERADMIN_EMAIL'),
];
