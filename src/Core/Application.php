<?php

namespace Abyss\Core;

use Dotenv\Dotenv;

class Application
{
    public static $base_path;
    public static $name;
    public static $env;
    public static $timezone;

    public static function start(string $base_path) : void
    {
        static::$base_path = $base_path;

        static::load_env();
    }

    public static function configure(array $config)
    {
        static::$name     = $config["name"];
        static::$env      = $config["env"];
        static::$timezone = $config["timezone"];
    }

    public static function load_env()
    {
        $dotenv = Dotenv::createImmutable(self::$base_path);

        $dotenv->load();
    }

    public static function get_base_path(string $path)
    {
        return self::$base_path . $path;
    }
}
