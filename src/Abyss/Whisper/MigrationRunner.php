<?php

namespace Abyss\Whisper;

use Abyss\Core\Application;

class MigrationRunner
{
    protected $migrations_path = "/app/database/migrations/";

    public function run(): void
    {
        echo "Running migrations...\n";

        // * Fetch migration files
        $migration_full_path = Application::get_base_path(
            $this->migrations_path
        );
        $migration_files = glob($migration_full_path . "*.php");

        foreach ($migration_files as $migration_file) {
            $migration_name = basename($migration_file, ".php");

            // * Get migration class
            $migration_class = require $migration_file;

            // * Drop the table
            $migration_class->down();

            // * Run the migration
            $migration_class->up();

            echo "Applied migration: $migration_name\n";
        }

        echo "Migrations completed.\n";
    }
}
