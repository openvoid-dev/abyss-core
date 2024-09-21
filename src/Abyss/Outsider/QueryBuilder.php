<?php

namespace Abyss\Outsider;

use PDO;

class QueryBuilder
{
    protected $connection;
    protected $table;
    protected $wheres     = [];
    protected $limit;
    protected $offset;
    protected $bindings   = [];

    public function __construct($table)
    {
        $this->table      = $table;
        $this->connection = Outsider::get_connection();  // Use the database connection from Outsider
    }

    public function where($column, $operator, $value)
    {
        $this->wheres[]   = "$column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    public function get()
    {
        $sql = "SELECT * FROM {$this->table}";

        if (! empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }

        if ($this->limit) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset) {
            $sql .= " OFFSET {$this->offset}";
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute($this->bindings);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function first()
    {
        $this->limit(1);
        $results = $this->get();
        return $results ? $results[0] : null;
    }

    public function insert(array $data)
    {
        $columns      = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql       = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $statement = $this->connection->prepare($sql);
        $statement->execute(array_values($data));

        return $this->connection->lastInsertId();
    }

    public function update(array $data, $primary_key, $id)
    {
        $setClause = implode(' = ?, ', array_keys($data)) . ' = ?';

        $sql       = "UPDATE {$this->table} SET $setClause WHERE $primary_key = ?";
        $statement = $this->connection->prepare($sql);

        $bindings   = array_values($data);
        $bindings[] = $id;

        return $statement->execute($bindings);
    }

    public function delete($primary_key, $id)
    {
        $sql       = "DELETE FROM {$this->table} WHERE $primary_key = ?";
        $statement = $this->connection->prepare($sql);
        return $statement->execute([$id]);
    }
}
