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
    public static function create($table, $callback): void
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
     */
    public static function drop($table): void
    {
        // * Get correct db driver
        $db_driver = Outsider::get_db_driver();

        $db_driver->destroy_table($table);
    }
}
