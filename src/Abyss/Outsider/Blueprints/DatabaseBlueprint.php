<?php

namespace Abyss\Outsider\Blueprints;

use Abyss\Outsider\Column;

interface DatabaseBlueprint
{
    /**
     * Create id columns with autoincrement and primary key true
     *
     * @param string $column_name
     * @return Column
     */
    public function id($column_name = "id"): Column;

    /**
     * Create a foreign id relationship
     *
     * @param string $column_name
     * @param string $table_name
     * @return Column
     **/
    public function foreign_id($column_name, $table_name): Column;

    /**
     * Create column of type string
     *
     * @param string $column_name
     * @param bool $auto_increment
     * @return Column
     */
    public function string($column_name, $auto_increment = false): Column;

    /**
     * Create column of type integer
     *
     * @param string $column_name
     * @param bool $auto_increment
     * @return Column
     */
    public function int($column_name, $auto_increment = false): Column;

    public function boolean(string $column_name): Column;

    /**
     * Create a column of type 'TIME'
     *
     * @param string $column_name
     * @return Column
     */
    public function time(string $column_name): Column;

    /**
     * Create a column of type timestamp
     *
     * @param string $column_name
     * @return Column
     */
    public function timestamp(string $column_name): Column;

    /**
     * Create created_at and updated_at timestamps
     *
     * @return void
     */
    public function timestamps(): void;

    /**
     * Get all of the columns in $sql query string
     *
     * @return string
     */
    public function get_columns(): string;
}
