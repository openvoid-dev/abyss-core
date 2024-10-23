<?php

namespace Abyss\Whisper;

use Abyss\Core\Application;
use Abyss\Helpers\StrHelper;
use Error;

class MakeController
{
    /**
     * Location of controllers directory
     *
     * @var string
     **/
    private $controllers_path = "/app/controllers/";

    /**
     * Controller name
     *
     * @var string
     **/
    private string $controller_name;

    /**
     * Name of the table for a controller
     *
     * @var string
     **/
    private string $table_name;

    /**
     * Create controller name and set table name
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        if (!isset($args[2])) {
            throw new Error(
                "No table name set, please provide a table name to create a Controller."
            );
        }

        $table_name = $args[2];

        // * Create a controller name based on given table name
        $controller_name = ucfirst($table_name);
        $controller_name = StrHelper::singularize($controller_name);

        $controller_name = "{$controller_name}Controller";

        $this->controller_name = $controller_name;
        $this->table_name = $table_name;
    }

    /**
     * Create a new controller file
     *
     * @return void
     **/
    public function make(): void
    {
        echo "Creating a new controller...\n";

        $controllers_folder_path = Application::get_base_path(
            $this->controllers_path
        );

        // * Create a new model file
        $controller_file = fopen(
            $controllers_folder_path . $this->controller_name . ".php",
            "w"
        );

        $controller_content = $this->_create_controller_content();

        fwrite($controller_file, $controller_content);
        fclose($controller_file);

        chmod($controllers_folder_path . $this->controller_name . ".php", 0777);

        echo "Created a new controller in /app/controllers/{$this->controller_name}.php\n";
    }

    /**
     * Create controller content
     *
     * @return string
     **/
    private function _create_controller_content(): string
    {
        $controller_content =
            '<?php

namespace App\Controllers;

use Abyss\Controller\Controller;

class ' .
            $this->controller_name .
            ' extends Controller
{
    public static function show(): void
    {}
};

';

        return $controller_content;
    }
}
