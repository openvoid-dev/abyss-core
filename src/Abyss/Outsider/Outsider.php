<?php

/**
 * Custom ORM built for Abyss called Outsider, taking a
 * similar approach to Laravel's Eloquent ORM.
 *
 */

namespace Abyss\Outsider;

use Abyss\Outsider\Blueprints\DatabaseBlueprint;
use Abyss\Outsider\Blueprints\MySQLBlueprint;
use Abyss\Outsider\Blueprints\SQLiteBlueprint;
use Abyss\Outsider\Drivers\DatabaseDriver;
use Abyss\Outsider\Drivers\MySQLDriver;
use Abyss\Outsider\Drivers\SQLiteDriver;
use Exception;
use PDO;
use PDOException;

class Outsider
{
    /**
     * Connection to the db
     *
     * @var null|PDO
     */
    protected static $connection = null;

    /**
     * Database driver of a selected database
     *
     * @var null|DatabaseDriver
     */
    public static $db_driver = null;

    /**
     * Connect to the database with the provided config
     *
     * @param array $config
     * @return void
     */
    public static function connect(array $config): void
    {
        // * Get DB driver
        self::$db_driver = $config["default"];
        $db_driver_config = $config["connections"][self::$db_driver];

        switch (self::$db_driver) {
            case "sqlite":
                self::connect_sqlite_driver($db_driver_config);
                break;
            case "mysql":
                self::connect_mysql_driver($db_driver_config);
                break;
            default:
                throw new Exception("Unsupported database driver.");
        }
    }

    /**
     * Get database connection
     *
     * @return PDO|null
     */
    public static function get_connection(): PDO|null
    {
        if (!self::$connection) {
            die("No database connection established.");
        }

        return self::$connection;
    }

    /**
     * Create a SQLite connection
     *
     * @param array $sqlite_config
     * @return void
     */
    public static function connect_sqlite_driver(array $sqlite_config): void
    {
        // * DSN for SQLITE is just 'sqlite:/path/to/database.sqlite'''
        $dsn = "sqlite:" . $sqlite_config["path"];

        try {
            self::$connection = new PDO($dsn);
            self::$connection->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
        } catch (PDOException $e) {
            die("SQLite connection failed: " . $e->getMessage());
        }
    }

    /**
     * Create a MySQL connection
     *
     * @param array $mysql_config
     * @return void
     */
    public static function connect_mysql_driver(array $mysql_config): void
    {
        $dsn = sprintf(
            "%s:host=%s;port=%s;dbname=%s;charset=%s",
            $mysql_config["driver"],
            $mysql_config["host"],
            $mysql_config["port"],
            $mysql_config["database"],
            $mysql_config["charset"]
        );

        try {
            self::$connection = new PDO(
                $dsn,
                $mysql_config["url"]
                    ? parse_url($mysql_config["url"], PHP_URL_USER)
                    : $mysql_config["username"],
                $mysql_config["url"]
                    ? parse_url($mysql_config["url"], PHP_URL_PASS)
                    : $mysql_config["password"]
            );
            self::$connection->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Get database driver based on selected database
     *
     * @return DatabaseDriver
     */
    public static function get_db_driver(): DatabaseDriver
    {
        switch (self::$db_driver) {
            case "sqlite":
                return new SQLiteDriver();
                break;
            case "mysql":
                return new MySQLDriver();
                break;
        }
    }

    /**
     * Get database blueprint, based on database driver
     *
     * @return DatabaseBlueprint
     */
    public static function get_db_blueprint(): DatabaseBlueprint
    {
        switch (self::$db_driver) {
            case "sqlite":
                return new SQLiteBlueprint();
                break;
            case "mysql":
                return new MySQLBlueprint();
                break;
        }
    }
}
