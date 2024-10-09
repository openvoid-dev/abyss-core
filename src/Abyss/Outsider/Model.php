<?php

namespace Abyss\Outsider;

abstract class Model
{
    public static $table;
    public static $primary_key = "id";
    protected static $attributes = [];

    public static function query(): QueryBuilder
    {
        return new QueryBuilder(static::get_table());
    }

    public static function find(mixed $id): array
    {
        return static::query()->where(self::get_primary_key(), "=", $id)->get();
    }

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

    public static function get_primary_key(): string
    {
        // Return the primary key from the child class if set, otherwise use the default
        return static::$primary_key ?? "id";
    }
}
