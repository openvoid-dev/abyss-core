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
     * Construct a new query class
     *
     * @param string $table
     * @param array $hidden
     * @return void
     **/
    public function __construct(string $table, array $hidden)
    {
        $this->table = $table;
        $this->hidden = $hidden;

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

    // public function insert(array $data)
    // {
    //     $columns = implode(", ", array_keys($data));
    //     $placeholders = implode(", ", array_fill(0, count($data), "?"));

    //     $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
    //     $statement = $this->connection->prepare($sql);
    //     $statement->execute(array_values($data));

    //     return $this->connection->lastInsertId();
    // }

    // public function update(array $data, $primary_key, $id)
    // {
    //     $setClause = implode(" = ?, ", array_keys($data)) . " = ?";

    //     $sql = "UPDATE {$this->table} SET $setClause WHERE $primary_key = ?";
    //     $statement = $this->connection->prepare($sql);

    //     $bindings = array_values($data);
    //     $bindings[] = $id;

    //     return $statement->execute($bindings);
    // }

    // public function delete($primary_key, $id)
    // {
    //     $sql = "DELETE FROM {$this->table} WHERE $primary_key = ?";
    //     $statement = $this->connection->prepare($sql);
    //     return $statement->execute([$id]);
    // }
}
