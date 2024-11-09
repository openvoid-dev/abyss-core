<?php

namespace Abyss\Core;

use Dotenv\Dotenv;

/**
 * Core Application class for configuring the application
 */
class Application
{
    /**
     * The base path of the Abyss framework.
     *
     * @var string
     */
    protected static $base_path;

    /**
     * The name of the project.
     *
     * @var string
     */
    protected static $name;

    /**
     * Type of the application environment.
     *
     * @var string
     */
    protected static $env;

    /**
     * Timezone of the application.
     *
     * @var string
     */
    protected static $timezone;

    /**
     * Start the Abyss framework and load the env configuration
     *
     * @param string $base_path
     * @return void
     */
    public static function start(string $base_path): void
    {
        self::$base_path = $base_path;

        self::load_env();
    }

    /**
     * Import and configure the app configuration
     *
     * @param array $config
     * @return void
     */
    public static function configure(array $config): void
    {
        self::$name = $config["name"];
        self::$env = $config["env"];
        self::$timezone = $config["timezone"];
    }

    /**
     * Get absolute path
     *
     * @param string $path
     * @return string
     */
    public static function get_base_path(string $path): string
    {
        return self::$base_path . $path;
    }

    /**
     * Load env file and its content
     *
     * @return void
     */
    private static function load_env(): void
    {
        $dotenv = Dotenv::createImmutable(self::$base_path);

        $dotenv->load();
    }
}
