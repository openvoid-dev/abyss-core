<?php

namespace Abyss\Whisper;

use Abyss\Core\Application;
use Abyss\Helpers\StrHelper;
use Error;

class MakeModel
{
    /**
     * Location of models directory
     *
     * @var string
     **/
    private $models_path = "/app/models/";

    /**
     * Model name
     *
     * @var string
     **/
    private string $model_name;

    /**
     * Name of the table for a model
     *
     * @var string
     **/
    private string $table_name;

    /**
     * Create model name and set table name
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        if (!isset($args[2])) {
            throw new Error(
                "No table name set, please provide a table name to create a Model."
            );
        }

        $table_name = $args[2];

        // * Create a model name based on given table name
        $model_name = ucfirst($table_name);
        $model_name = StrHelper::singularize($model_name);

        $model_name = "{$model_name}Model";

        $this->model_name = $model_name;
        $this->table_name = $table_name;
    }

    /**
     * Create a new model file
     *
     * @return void
     **/
    public function make(): void
    {
        echo "Creating a new model...\n";

        $models_folder_path = Application::get_base_path($this->models_path);

        // * Create a new model file
        $model_file = fopen(
            $models_folder_path . $this->model_name . ".php",
            "w"
        );

        $model_content = $this->_create_model_content();

        fwrite($model_file, $model_content);
        fclose($model_file);

        chmod($models_folder_path . $this->model_name . ".php", 0777);

        echo "Created a new model in /app/models/{$this->model_name}.php\n";
    }

    /**
     * Create model content
     *
     * @return string
     **/
    private function _create_model_content(): string
    {
        $model_content =
            '<?php

namespace App\Models;

use Abyss\Outsider\HasFactory;
use Abyss\Outsider\Model;

class ' .
            $this->model_name .
            ' extends Models
{
    /**
    * Uncomment this line if you are using
    * a factory for this model
    *
    **/
    // use HasFactory;

    /**
     * Table name of the Model.
     *
     * @var string
     */
    public static $table = "' .
            $this->table_name .
            '";

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected static $primary_key = "id";

    /**
     * Columns that should never be sent from the server
     *
     * @var array
     */
    protected static $hidden = [];

    /**
     * Columns that are allowed to have
     * values inserted into
     *
     * @var array
     */
    protected static $fillable = [];
};

';

        return $model_content;
    }
}
