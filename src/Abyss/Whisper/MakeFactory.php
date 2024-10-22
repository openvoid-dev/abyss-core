<?php

namespace Abyss\Whisper;

use Abyss\Core\Application;
use Abyss\Helpers\StrHelper;
use Error;

class MakeFactory
{
    /**
     * Location of database factories
     *
     * @var string
     **/
    private $factories_path = "/app/database/factories/";

    /**
     * Factory name
     *
     * @var string
     **/
    private string $factory_name;

    /**
     * Name of the table for a factory
     *
     * @var string
     **/
    private string $table_name;

    /**
     * Create factory name and set table name
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        if (!isset($args[2])) {
            throw new Error(
                "No table name set, please provide a table name to create a Factory Class"
            );
        }

        $table_name = $args[2];

        // * Create a factory name based on given table name
        $factory_name = ucfirst($table_name);
        $factory_name = StrHelper::singularize($factory_name);

        $factory_name = "{$factory_name}Factory";

        $this->factory_name = $factory_name;
        $this->table_name = $table_name;
    }

    /**
     * Create a new factory file
     *
     * @return void
     **/
    public function make(): void
    {
        echo "Creating a new factory...\n";

        $factories_folder_path = Application::get_base_path(
            $this->factories_path
        );

        // * Create a new factory file
        $factory_file = fopen(
            $factories_folder_path . $this->factory_name . ".php",
            "w"
        );

        $factory_content = $this->_create_factory_content();

        fwrite($factory_file, $factory_content);
        fclose($factory_file);

        chmod($factories_folder_path . $this->factory_name . ".php", 0777);

        echo "Created a new factory in /app/database/factories/{$this->factory_name}.php\n";
    }

    /**
     * Create factory content
     *
     * @return string
     **/
    private function _create_factory_content(): string
    {
        $factory_content =
            '<?php
namespace App\Database\Factories;

use Abyss\Outsider\Factory;
use Abyss\Helpers\Helper;

class ' .
            $this->factory_name .
            ' extends Factory
{
    /**
    * Here you define your factory,
    * this is an example of how
    * it could look like
    *
    * @return array
    **/
    public function definition(): array
    {
        return [
            "title" => Helper::fake()->text(),
            "description" => Helper::fake()->realText(),
        ];
    }
};
';

        return $factory_content;
    }
}
