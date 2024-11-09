<?php

/**
 * Nebula AUTH system config
 **/

use Abyss\Helpers\Helper;

return [
    /**
     * JWT Secret
     **/

    "jwt_secret" => Helper::env("JWT_SECRET", "sifra123"),
];
