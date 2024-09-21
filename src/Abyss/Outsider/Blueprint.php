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

    public function boolean($column_name)
    {
        $column          = new Column($column_name, "BOOL", false);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * Create a column of type 'TIME'
     *
     * @param string $column_name
     * @return Column
     */
    public function time($column_name)
    {
        $column          = new Column($column_name, "TIME", false);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * Create a column of type timestamp
     *
     * @param string $column_name
     * @return Column
     */
    public function timestamp($column_name)
    {
        $column          = new Column($column_name, "TIMESTAMP", false);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * Create created_at and updated_at timestamps
     *
     * @return void
     */
    public function timestamps()
    {
        $this->timestamp("created_at")->nullable()->default("CURRENT_TIMESTAMP");

        $this->timestamp("updated_at")->nullable()->default("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    }

    /**
     * Get all of the columns in $sql query string
     *
     * @return string
     */
    public function to_sql()
    {
        $columns = array_map(fn (Column $col) => $col->to_sql(), $this->columns);
        $columns = implode(", ", $columns);

        return $columns;
    }
}
