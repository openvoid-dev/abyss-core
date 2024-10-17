<?php

namespace Abyss\Outsider;

use Error;
use Exception;
use PDO;

class QueryBuilder
{
    /**
     * DB connection
     *
     * @var PDO
     **/
    protected PDO $connection;

    /**
     * Table name
     *
     * @var string
     **/
    protected $table;

    /**
     * All of the queries where clauses
     *
     * @var array
     **/
    protected $wheres = [];

    /**
     * What to select from db, default is all (*)
     *
     * @var array
     **/
    protected $selects = [];

    /**
     * Value that represents the limit to how
     * many rows a query should get
     *
     * @var int
     **/
    protected $limit;

    /**
     * Value that represents the offset
     *
     * @var int
     **/
    protected $offset;

    /**
     * Value that represents the order by
     *
     * @var string
     **/
    protected $order_by;

    /**
     * All of the bindings to set in execute function
     *
     * @var array
     **/
    protected $bindings = [];

    /**
     * Columns that should never be sent
     * from the server
     *
     * @var array
     **/
    protected $hidden = [];

    /**
     * All columns that are allowed to
     * be assigned a value to
     *
     * @var array
     **/
    protected $fillable = [];

    /**
     * Name of the primary key
     *
     * @var string
     **/
    protected $primary_key = "id";

    /**
     * Construct a new query class
     *
     * @param string $table
     * @param array $hidden
     * @param string $primary_key
     * @param array $fillable
     * @return void
     **/
    public function __construct(
        string $table,
        array $hidden,
        string $primary_key,
        array $fillable
    ) {
        $this->table = $table;
        $this->hidden = $hidden;
        $this->primary_key = $primary_key;
        $this->fillable = $fillable;

        $this->connection = Outsider::get_connection();
    }

    /**
     * Set a basic where clause to the query
     *
     * @param mixed $params
     * @return QueryBuilder
     **/
    public function where(...$params): QueryBuilder
    {
        if (count($params) < 2) {
            throw new Error("No value defined...");
        }

        $column = $params[0];
        $operator = "=";
        $value = $params[1];

        // * If there are only 2 params treat $operator as '='
        if (count($params) > 2) {
            $operator = $params[1];
            $value = $params[2];
        }

        $this->wheres[] = [
            "statement" => "$column $operator ?",
            "type" => "default",
        ];
        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Set an "or where" clause to the query
     *
     * @param mixed $params
     * @return QueryBuilder
     **/
    public function or_where(...$params): QueryBuilder
    {
        if (count($params) < 2) {
            throw new Error("No value defined...");
        }

        // * If there are only 2 params treat $operator as '='
        $column = $params[0];
        $operator = "=";
        $value = $params[1];

        if (count($params) > 2) {
            $operator = $params[1];
            $value = $params[2];
        }

        $this->wheres[] = [
            "statement" => "$column $operator ?",
            "type" => "or",
        ];
        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Filter results between two values
     *
     * @param string $column
     * @param array $values
     * @return QueryBuilder
     **/
    public function where_between($column, $values): QueryBuilder
    {
        $this->wheres[] = [
            "statement" => "$column BETWEEN ? AND ?",
            "type" => "default",
        ];
        $this->bindings[] = $values[0];
        $this->bindings[] = $values[1];

        return $this;
    }

    /**
     * Filter results where the column value is
     * outside the range of 2 values
     *
     * @param string $column
     * @param array $values
     * @return QueryBuilder
     **/
    public function where_outside($column, $values): QueryBuilder
    {
        $this->wheres[] = [
            "statement" => "$column NOT BETWEEN ? AND ?",
            "type" => "default",
        ];
        $this->bindings[] = $values[0];
        $this->bindings[] = $values[1];

        return $this;
    }

    /**
     * Filters results where the column value is
     * in a given array of values.
     *
     * @param string $column
     * @param array $values
     * @return QueryBuilder
     **/
    public function where_in($column, $values): QueryBuilder
    {
        $placeholders = implode(",", array_fill(0, count($values), "?"));

        $this->wheres[] = [
            "statement" => "$column IN ($placeholders)",
            "type" => "default",
        ];
        array_push($this->bindings, ...$values);

        return $this;
    }

    /**
     * Filters results where the column value is
     * not in a given array of values.
     *
     * @param string $column
     * @param array $values
     * @return QueryBuilder
     **/
    public function where_not_in($column, $values): QueryBuilder
    {
        $placeholders = implode(",", array_fill(0, count($values), "?"));

        $this->wheres[] = [
            "statement" => "$column NOT IN ($placeholders)",
            "type" => "default",
        ];
        array_push($this->bindings, ...$values);

        return $this;
    }

    /**
     * Filters results where the column is NULL.
     *
     * @param string $column
     * @return QueryBuilder
     **/
    public function where_null($column): QueryBuilder
    {
        $this->wheres[] = [
            "statement" => "$column IS NULL",
            "type" => "default",
        ];

        return $this;
    }

    /**
     * Filters results where the column is NOT NULL.
     *
     * @param string $column
     * @return QueryBuilder
     **/
    public function where_not_null($column): QueryBuilder
    {
        $this->wheres[] = [
            "statement" => "$column IS NOT NULL",
            "type" => "default",
        ];

        return $this;
    }

    public function add_wheres_to_query(): string
    {
        $query = " WHERE 1=1 ";

        foreach ($this->wheres as $where) {
            switch ($where["type"]) {
                case "default":
                    $query .= " AND {$where["statement"]}";
                    break;
                case "or":
                    $query .= " OR {$where["statement"]}";
                    break;
            }
        }

        return $query;
    }

    /**
     * Set select values
     *
     * @param string $column
     * @return QueryBuilder
     **/
    public function select(string ...$column): QueryBuilder
    {
        $this->selects = $column;

        return $this;
    }

    /**
     * Set a limit
     *
     * @param int $limit
     * @return QueryBuilder
     **/
    public function limit(int $limit): QueryBuilder
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Set offset
     *
     * @param int $$offset
     * @return QueryBuilder
     **/
    public function offset(int $offset): QueryBuilder
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Order results
     *
     * @param string $column
     * @param string $value
     * @return QueryBuilder
     **/
    public function order_by($column, $value): QueryBuilder
    {
        $this->order_by = "ORDER BY $column $value";

        return $this;
    }

    /**
     * Find a first row
     *
     * @return array
     **/
    public function find(): array
    {
        return $this->limit(1)->find_many();
    }

    /**
     * Find many rows
     *
     * @return array
     **/
    public function find_many(): array
    {
        $query = "SELECT * FROM {$this->table}";

        if (!empty($this->selects)) {
            $selects = implode(", ", $this->selects);

            $query = "SELECT $selects FROM {$this->table}";
        }

        if (!empty($this->wheres)) {
            $query .= $this->add_wheres_to_query();
        }

        if ($this->limit) {
            $query .= " LIMIT {$this->limit}";
        }

        if ($this->offset) {
            $query .= " OFFSET {$this->offset}";
        }

        if ($this->order_by) {
            $query .= " {$this->order_by}";
        }

        $statement = $this->connection->prepare($query);

        try {
            $statement->execute($this->bindings);
        } catch (Exception $error) {
            throw new Error($error);
        }

        $data = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($this->hidden)) {
            foreach ($data as $key => $row) {
                foreach ($this->hidden as $hidden_column) {
                    unset($data[$key][$hidden_column]);
                }
            }
        }

        return $data;
    }

    /**
     * Create a new row
     *
     * @param array $data
     * @return void
     **/
    public function create(array $data): void
    {
        $columns = [];
        $bindings = [];
        $values = [];

        foreach ($data as $key => $row) {
            if (!in_array($key, $this->fillable)) {
                continue;
            }

            $columns[] = $key;
            $bindings[] = ":$key";
            $values[":$key"] = $row;
        }

        $columns_string = implode(", ", $columns);
        $bindings_string = implode(", ", $bindings);

        $statement = $this->connection->prepare(
            "INSERT INTO {$this->table} ($columns_string) VALUES ($bindings_string)"
        );

        try {
            $statement->execute($values);
        } catch (Exception $error) {
            throw new Error($error);
        }
    }

    /**
     * Create many rows
     *
     * @param array $data
     * @return void
     **/
    public function create_many(array ...$data): void
    {
        foreach ($data as $row) {
            // * Call create method for each array
            $this->create($row);
        }
    }

    /**
     * Update a row in a table
     *
     * @param array $data
     * @return void
     **/
    public function update(array $data): void
    {
        $this->limit(1)->update_many($data);
    }

    /**
     * Update multiple rows in a table
     *
     * @param array $data
     * @return void
     **/
    public function update_many(array $data): void
    {
        $set_clause = implode(" = ?, ", array_keys($data)) . " = ?";
        $values = array_values($data);
        $values = array_merge($values, $this->bindings);

        $query = "UPDATE {$this->table} SET $set_clause";

        if (!empty($this->wheres)) {
            $query .= $this->add_wheres_to_query();
        }

        if ($this->limit) {
            $query .= " LIMIT {$this->limit}";
        }

        $statement = $this->connection->prepare($query);

        try {
            $statement->execute($values);
        } catch (Exception $error) {
            throw new Error($error);
        }
    }

    /**
     * Destroy a row in a table
     *
     * @return void
     **/
    public function destroy(): void
    {
        $this->limit(1)->destroy_many();
    }

    /**
     * Destroy multiple rows in a table
     *
     * @return void
     **/
    public function destroy_many(): void
    {
        if (empty($this->wheres)) {
            throw new Error("No where clause added");
        }

        $query = "DELETE FROM {$this->table} ";
        $query .= $this->add_wheres_to_query();

        if ($this->limit) {
            $query .= " LIMIT {$this->limit}";
        }

        $statement = $this->connection->prepare($query);

        try {
            $statement->execute($this->bindings);
        } catch (Exception $error) {
            throw new Error($error);
        }
    }

    /**
     * Create a custom raw sql query with bindings
     *
     * @param string $query
     * @param array $bindings
     * @return array|bool
     **/
    public function raw_sql(string $query, array $bindings = []): array|bool
    {
        $statement = $this->connection->prepare($query);

        try {
            $statement->execute($bindings);
        } catch (Exception $error) {
            throw new Error($error);
        }

        $data = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($this->hidden)) {
            foreach ($data as $key => $row) {
                foreach ($this->hidden as $hidden_column) {
                    unset($data[$key][$hidden_column]);
                }
            }
        }

        return $data;
    }

    /**
     * Show a select statement with bindings
     *
     * @return array
     **/
    public function show_select_statement(): array
    {
        $query = "SELECT * FROM {$this->table}";

        if (!empty($this->selects)) {
            $selects = implode(", ", $this->selects);

            $query = "SELECT $selects FROM {$this->table}";
        }

        if (!empty($this->wheres)) {
            $query .= $this->add_wheres_to_query();
        }

        if ($this->limit) {
            $query .= " LIMIT {$this->limit}";
        }

        if ($this->offset) {
            $query .= " OFFSET {$this->offset}";
        }

        if ($this->order_by) {
            $query .= " {$this->order_by}";
        }

        $statement = $this->connection->prepare($query);

        return [
            "statement" => $statement,
            "bindings" => $this->bindings,
        ];
    }

    /**
     * Get last created row
     *
     * @return array
     **/
    public function last(): array
    {
        $last_inserted_id = $this->connection->lastInsertId($this->primary_key);

        return $this->find($last_inserted_id);
    }
}
