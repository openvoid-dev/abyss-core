<?php

namespace Abyss\Outsider;

class Blueprint
{
    /**
     * All columns for a table
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Create id columns with autoincrement and primary key true
     *
     * @param string $column_name
     * @return Column
     */
    public function id($column_name = "id")
    {
        $column          = new Column($column_name, "INT", true, true);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * Create column of type string
     *
     * @param string $column_name
     * @param bool $auto_increment
     * @return Column
     */
    public function string($column_name, $auto_increment = false)
    {
        $column          = new Column($column_name, 'VARCHAR(255)', $auto_increment);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * Create column of type integer
     *
     * @param string $column_name
     * @param bool $auto_increment
     * @return Column
     */
    public function int($column_name, $auto_increment = false)
    {
        $column          = new Column($column_name, 'INT', $auto_increment);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * Get all of the columns in $sql query string
     *
     * @return string
     */
    public function to_sql()
    {
        return implode(", ", array_map(fn ($col) => $col->to_sql(), $this->columns));
    }
}
