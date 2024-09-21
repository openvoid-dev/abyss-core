<?php

namespace Abyss\Outsider;

use Abyss\Core\Helper;
use Closure;

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

        Helper::dd($sql);
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
        $sql = "DROP TABLE {$table}";

        var_dump($sql);
    }
}


