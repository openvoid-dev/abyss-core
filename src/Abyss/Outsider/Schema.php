<?php

namespace Abyss\Outsider;

use Closure;
use Exception;

class Schema
{
    /**
     * Create new schema for a table
     *
     * @param string $table
     * @param Closure $callback
     * @return void
     * @throws Exception
     */
    public static function create(string $table, Closure $callback): void
    {
        // * Get correct driver and blueprint based on the used database
        $db_driver = Outsider::get_db_driver();
        $db_blueprint = Outsider::get_db_blueprint();

        // * Create new blueprint
        $callback($db_blueprint);

        // * Get columns
        $columns = $db_blueprint->get_columns();

        // * Create a table
        $db_driver->create_table($table, $columns);
    }

    /**
     * Delete a table
     *
     * @param string $table
     * @return void
     * @throws Exception
     */
    public static function drop(string $table): void
    {
        // * Get correct db driver
        $db_driver = Outsider::get_db_driver();

        $db_driver->destroy_table($table);
    }
}
