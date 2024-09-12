<?php

/**
 * Core application config
 */

use Abyss\Core\Helper;

return [
    /**
     * Application name
     */


    'name'     => Helper::env('APP_NAME', 'Abyss'),

    /**
     * Application environment
     */

    'env'      => Helper::env("APP_ENV", "development"),

    /**
     * Application timezone
     */

    'timezone' => Helper::env("APP_TIMEZONE", "Europe/Zagreb"),
];
