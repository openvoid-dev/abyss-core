<?php

namespace Abyss\Whisper;

use Abyss\Core\Application;

class MigrationRunner
{
    /**
     * Location of database migrations
     *
     * @var string
     **/
    protected $migrations_path = "/app/database/migrations/";

    /**
     * All of the migration classes
     *
     * @var array
     **/
    protected $migrations = [];

    /**
     * Run the migrations
     *
     * @return void
     **/
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

            // * If migration has no dependecies put it at the
            // * beggining of an array
            if (empty($migration_class->depends_on)) {
                array_unshift($this->migrations, [
                    "class" => $migration_class,
                    "migration_name" => $migration_name,
                ]);

                continue;
            }

            $this->migrations[] = [
                "class" => $migration_class,
                "migration_name" => $migration_name,
            ];
        }

        // * Drop all tables in reverse order
        foreach (array_reverse($this->migrations) as $migration) {
            $migration["class"]->down();

            echo "Droped : {$migration["migration_name"]}\n";
        }

        // * Create all tables
        foreach ($this->migrations as $migration) {
            $migration["class"]->up();

            echo "Created : {$migration["migration_name"]}\n";
        }

        echo "Migrations completed.\n";
    }
}
