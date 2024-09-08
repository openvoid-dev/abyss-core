<?php

namespace Abyss\Core;

use Abyss\Horizon\Horizon;

class Application
{
    public static $base_path;

    public static function configure(string $base_path)
    {
        static::$base_path = $base_path;
    }

    public static function handle_request()
    {
        require static::$base_path . '/app/routes/web.php';

        try {
            Horizon::route();
        } catch (err) {
            return Horizon::redirect(Horizon::previousUrl());
        }
    }

    public static function get_base_path(string $path)
    {
        return self::$base_path . $path;
    }
}
