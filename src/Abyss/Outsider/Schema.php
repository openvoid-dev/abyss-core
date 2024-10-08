<?php

namespace Abyss\Outsider;

class Schema
{
    /**
     * Create new schema for a table
     *
     * @param string $table
     * @param Closure $callback
     * @return void
     */
    public static function create($table, $callback)
    {
        // * Create new blueprint
        $blueprint = new Blueprint();
        $callback($blueprint);

        // * Get columns
        $columns = $blueprint->to_sql();

        // * Create query
        $sql = "CREATE TABLE $table ($columns)";

        // * Execute the query
        self::_execute($sql);
    }

    /**
     * Delete a table
     *
     * @param string $table
     * @return void
     */
    public static function drop($table)
    {
        // * Create query
        $sql = "DROP TABLE IF EXISTS {$table}";

        // * Execute the query
        self::_execute($sql);
    }

    /**
     * Execute the given sql query
     *
     * @param string $query
     * @return void
     */
    private static function _execute($query)
    {
        $db = Outsider::get_connection();

        $statement = $db->prepare($query);
        $statement->execute();
    }
}
