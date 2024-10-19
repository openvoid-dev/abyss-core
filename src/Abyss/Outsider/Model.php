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
    protected static $primary_key = "id";

    /**
     * All columns that should never be
     * sent from the server
     *
     * @var array
     **/
    protected static $hidden = [];

    /**
     * All columns that are allowed to
     * be assigned a value to
     *
     * @var array
     **/
    protected static $fillable = [];

    /**
     * Create a new query
     *
     * @return QueryBuilder
     **/
    public static function query(): QueryBuilder
    {
        return new QueryBuilder(
            get_called_class(),
            static::get_table(),
            static::get_hidden_columns(),
            static::get_primary_key(),
            static::get_fillable_columns()
        );
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

    /**
     * Get models fillable columns
     *
     * @return array
     **/
    public static function get_fillable_columns(): array
    {
        return static::$fillable;
    }

    public static function get_relations(): array
    {
        return static::$relations;
    }

    public static function has_many($related_model, $foreign_key, $primary_key)
    {
        return [
            "type" => "has_many",
            "model" => $related_model,
            "foreign_key" => $foreign_key,
            "primary_key" => $primary_key,
        ];
    }
}
