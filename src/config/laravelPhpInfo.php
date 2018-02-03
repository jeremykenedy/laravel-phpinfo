<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel PHP Info settings
    |--------------------------------------------------------------------------
    */

    // The parent blade file
    'laravelPhpInfoBladeExtended'   => 'layouts.app',

    // Enable `auth` middleware
    'authEnabled'                   => true,

    // Enable Optional Roles Middleware
    'rolesEnabled'                  => false,

    // Optional Roles Middleware
    'rolesMiddlware'                => 'role:admin',

];
