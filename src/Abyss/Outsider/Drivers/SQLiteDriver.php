<?php

namespace Abyss\Outsider\Drivers;

use Abyss\Outsider\Outsider;

class SQLiteDriver implements DatabaseDriver
{
    private $db = null;

    public function __construct()
    {
        $this->db = Outsider::get_connection();
    }

    public function create_table(string $table, string $columns): void
    {
        $query = "CREATE TABLE $table ($columns)";

        $statement = $this->db->prepare($query);
        $statement->execute();
    }

    public function update_table(string $table, array $columns): void
    {
    }

    public function destroy_table(string $table): void
    {
        $query = "DROP TABLE IF EXISTS $table";

        $statement = $this->db->prepare($query);
        $statement->execute();
    }

    public function insert(string $table, array $data): void
    {
    }

    public function update(string $table, array $data, array $conditions): void
    {
    }

    public function destroy(string $table, array $conditions): void
    {
    }
}
