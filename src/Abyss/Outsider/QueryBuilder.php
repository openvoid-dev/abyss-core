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
        $this->wheres[] = "$column $operator :$column";
        $this->bindings[":$column"] = $value;

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
        $sql = "SELECT * FROM {$this->table}";

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(" AND ", $this->wheres);
        }

        if ($this->limit) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset) {
            $sql .= " OFFSET {$this->offset}";
        }

        $statement = $this->connection->prepare($sql);

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
     * Find a row based on its primary key value
     *
     * @param mixed $id
     * @return array
     **/
    public function find(mixed $id): array
    {
        return $this->where($this->primary_key, "=", $id)->get();
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
     * Update a row in a table
     *
     * @param array $data
     * @param string $primary_key
     * @param mixed $primary_key_value
     * @return void
     **/
    public function update(
        array $data,
        string $primary_key,
        mixed $primary_key_value
    ): void {
        $set_clause = implode(" = ?, ", array_keys($data)) . " = ?";
        $values = array_values($data);
        $values[] = $primary_key_value;

        $query = "UPDATE {$this->table} SET $set_clause WHERE $primary_key = ?";

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
    public function destroy(string $primary_key, mixed $primary_key_value): void
    {
        $query = "DELETE FROM {$this->table} WHERE $primary_key = :$primary_key";
        $values = [
            ":$primary_key" => $primary_key_value,
        ];
        $statement = $this->connection->prepare($query);

        try {
            $statement->execute($values);
        } catch (Exception $error) {
            throw new Error($error);
        }
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
