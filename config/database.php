<?php

/**
 * Core database config
 */

use Abyss\Core\Application;
use Abyss\Helpers\Helper;

return [
    "default" => Helper::env("DB_CONNECTION", "sqlite"),

    "connections" => [
        "sqlite" => [
            "driver" => "sqlite",
            "path" => Application::get_base_path(
                "/app/database/database.sqlite"
            ),
        ],
        "mysql" => [
            "driver" => "mysql",
            "url" => Helper::env("DB_URL"),
            "host" => Helper::env("DB_HOST", "localhost"),
            "port" => Helper::env("DB_PORT", 3306),
            "database" => Helper::env("DB_DATABASE", "abyss"),
            "username" => Helper::env("DB_USERNAME", "root"),
            "password" => Helper::env("DB_PASSWORD", ""),
            "charset" => Helper::env("DB_CHARSET", "utf8mb4"),
            "collation" => Helper::env("DB_COLLATION", "utf8mb4_unicode_ci"),
        ],
    ],
];
