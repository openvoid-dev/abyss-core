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
     * Set where clause
     *
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @return QueryBuilder
     **/
    public function where(
        string $column,
        string $operator,
        mixed $value
    ): QueryBuilder {
        $this->wheres[] = "$column $operator ?";
        $this->bindings[] = $value;

        return $this;
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
     * Create a GET query
     *
     * @return array|bool
     **/
    public function get(): array|bool
    {
        $query = "SELECT * FROM {$this->table}";

        if (!empty($this->wheres)) {
            $query .= " WHERE " . implode(" AND ", $this->wheres);
        }

        if ($this->limit) {
            $query .= " LIMIT {$this->limit}";
        }

        if ($this->offset) {
            $query .= " OFFSET {$this->offset}";
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
     * Get only the first row
     *
     * @return array|bool
     **/
    public function first(): array|bool
    {
        $this->limit(1);
        $results = $this->get();

        return $results ? $results[0] : null;
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
            $query .= " WHERE " . implode(" AND ", $this->wheres);
        }

        if ($this->limit) {
            $query .= " LIMIT {$this->limit}";
        }

        if ($this->offset) {
            $query .= " OFFSET {$this->offset}";
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
            $query .= " WHERE " . implode(" AND ", $this->wheres);
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

        $query =
            "DELETE FROM {$this->table} WHERE " .
            implode(" AND ", $this->wheres);

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
