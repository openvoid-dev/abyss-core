<?php

namespace Abyss\Outsider;

use PDO;
use PDOException;

class Outsider
{
    protected static $connection = null;

    public static function connect(array $config)
    {
        $connection_config = $config['connections'][$config['default']];

        $dsn = sprintf(
            "%s:host=%s;port=%s;dbname=%s;charset=%s",
            $connection_config['driver'],
            $connection_config['host'],
            $connection_config['port'],
            $connection_config['database'],
            $connection_config['charset']
        );

        try {
            self::$connection = new PDO(
                $dsn,
                $connection_config['url'] ? parse_url($connection_config['url'], PHP_URL_USER) : $connection_config['username'],
                $connection_config['url'] ? parse_url($connection_config['url'], PHP_URL_PASS) : $connection_config['password']
            );
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getConnection()
    {
        if (! self::$connection) {
            die("No database connection established.");
        }

        return self::$connection;
    }
}
