<?php

namespace Abyss\Outsider;

abstract class Model
{
    /**
     * Table name
     *
     * @var string
     **/
    public static $table;

    /**
     * Name of the primary key
     *
     * @var string
     **/
    public static $primary_key = "id";

    /**
     * All columns that should never be
     * sent from the server
     *
     * @var array
     **/
    protected static $hidden = [];

    /**
     * Create a new query
     *
     * @return QueryBuilder
     **/
    public static function query(): QueryBuilder
    {
        return new QueryBuilder(
            static::get_table(),
            static::get_hidden_columns()
        );
    }

    /**
     * Find a row based on its primary key value
     *
     * @param mixed $id
     * @return array
     **/
    public static function find(mixed $id): array
    {
        return static::query()->where(self::get_primary_key(), "=", $id)->get();
    }

    /**
     * Get models table name based on its name
     *
     * @return string
     **/
    public static function get_table(): string
    {
        if (!empty(static::$table)) {
            return static::$table;
        }

        $table_name = new \ReflectionClass(static::class);
        $table_name = str_replace("Model", "", $table_name->getShortName());
        $table_name = strtolower($table_name) . "s";

        return $table_name;
    }

    /**
     * Get models primary key name
     *
     * @return string
     **/
    public static function get_primary_key(): string
    {
        // Return the primary key from the child class if set, otherwise use the default
        return static::$primary_key ?? "id";
    }

    /**
     * Get models hidden columns
     *
     * @return array
     **/
    public static function get_hidden_columns(): array
    {
        return static::$hidden;
    }
}
