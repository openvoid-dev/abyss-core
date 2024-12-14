<?php

namespace Abyss\Outsider\Blueprints;

use Abyss\Outsider\Column;

class SQLiteBlueprint implements DatabaseBlueprint
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
    public function id($column_name = "id"): Column
    {
        $column          = new Column($column_name, "INTEGER", true, true);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * Create a foreign id relationship
     *
     * @param string $column_name
     * @param string $table_name
     * @return Column
     **/
    public function foreign_id($column_name, $table_name): Column
    {
        $column = new Column($column_name, "INTEGER", false);
        $column->foreign_key($table_name);

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
    public function string($column_name, $auto_increment = false): Column
    {
        $column          = new Column($column_name, "VARCHAR(255)", $auto_increment);
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
    public function int($column_name, $auto_increment = false): Column
    {
        $column          = new Column($column_name, "INTEGER", $auto_increment);
        $this->columns[] = $column;

        return $column;
    }

    public function boolean($column_name): Column
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
    public function time($column_name): Column
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
    public function timestamp($column_name): Column
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
    public function timestamps(): void
    {
        $this->timestamp("created_at")
            ->nullable()
            ->default("CURRENT_TIMESTAMP");

        $this->timestamp("updated_at")
            ->nullable()
            ->default("CURRENT_TIMESTAMP");
    }

    /**
     * Get all of the columns in $sql query string
     *
     * @return string
     */
    public function get_columns(): string
    {
        // * All the columns
        $columns_query = [];
        // * Query to be added at the end for stuff like foreign id
        $query_end = [];

        foreach ($this->columns as $column) {
            $columns_query[] = $column->to_sql();

            // * If a columns is a foreign key column, we must add constraint
            // * and refrence at the end of the query for it
            if ($column->is_foreign_key) {
                $query_end[] = "CONSTRAINT fk_{$column->name} FOREIGN KEY ({$column->name}) REFERENCES {$column->foreign_key_table}({$column->name}) ON DELETE CASCADE";
            }
        }

        $query = array_merge($columns_query, $query_end);
        $query = implode(", ", $query);

        return $query;
    }
}
