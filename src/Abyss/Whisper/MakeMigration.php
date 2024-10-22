<?php

namespace Abyss\Whisper;

use Abyss\Core\Application;
use Abyss\Helpers\StrHelper;
use Error;

class MakeMigration
{
    /**
     * Location of database migrations
     *
     * @var string
     **/
    private $migrations_path = "/app/database/migrations/";

    /**
     * Migration name
     *
     * @var string
     **/
    private string $migration_name;

    /**
     * Name of the table to be created
     *
     * @var string
     **/
    private string $table_name;

    /**
     * Create migration name and set table name
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        if (!isset($args[2])) {
            throw new Error(
                "No table name set, please provide a table name to create a Migration Class"
            );
        }

        $table_name = $args[2];

        // * Create a migration name based on given table name
        $migration_name = ucfirst($table_name);
        $migration_name = StrHelper::singularize($migration_name);

        $migration_name = "Create{$migration_name}Migration";

        $this->migration_name = $migration_name;
        $this->table_name = $table_name;
    }

    /**
     * Create a new migration file
     *
     * @return void
     **/
    public function make(): void
    {
        echo "Creating a new migration...\n";

        $migration_folder_path = Application::get_base_path(
            $this->migrations_path
        );

        // * Create a new migration file
        $migration_file = fopen(
            $migration_folder_path . $this->migration_name . ".php",
            "w"
        );

        $migration_content = $this->_create_migration_content();

        fwrite($migration_file, $migration_content);
        fclose($migration_file);

        chmod($migration_folder_path . $this->migration_name . ".php", 0777);

        echo "Created a new migration in /app/database/migrations/{$this->migration_name}.php\n";
    }

    /**
     * Create migration content
     *
     * @return string
     **/
    private function _create_migration_content(): string
    {
        $migration_content =
            '<?php

use Abyss\Outsider\Blueprint;
use Abyss\Outsider\Migration;
use Abyss\Outsider\Schema;

return new class extends Migration
{
    /**
    * Dependecy on other tables
    *
    * If this table is dependent on some other tables
    * add them to this array
    **/
    public array $depends_on = [];

    public function up()
    {
        Schema::create("' .
            $this->table_name .
            '", function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop("' .
            $this->table_name .
            '");
    }
};
';

        return $migration_content;
    }
}
