<?php

namespace Abyss\Outsider\Drivers;

interface DatabaseDriver
{
    public function create_table(string $table, string $columns): void;

    public function update_table(string $table, array $columns): void;

    public function destroy_table(string $table): void;

    public function insert(string $table, array $data): void;

    public function update(string $table, array $data, array $conditions): void;

    public function destroy(string $table, array $conditions): void;
}
