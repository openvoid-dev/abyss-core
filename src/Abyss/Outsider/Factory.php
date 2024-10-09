<?php

namespace Abyss\Outsider;

abstract class Factory
{
    protected string $table_name;

    protected int $count = 1;

    /**
     * Run the factory
     *
     *
     */
    public function create(): void
    {
        for ($i = 0; $i < $this->count; $i++) {
            $columns = [];
            $placeholders = [];
            $values = [];

            // * Get factory definition
            $columns_to_seed = $this->definition();

            // * Separate columns from values
            foreach ($columns_to_seed as $column => $value) {
                $columns[] = $column;
                $placeholders[] = ":$column";
                $values[":$column"] = $value;
            }

            // * Prepare columns and placeholders to strings
            $columns_string = implode(", ", $columns);
            $placeholders_string = implode(", ", $placeholders);

            if (empty($this->table_name)) {
                throw new \Exception("Table name is not set.");
            }

            // * Create query
            $sql = "INSERT INTO {$this->table_name} ($columns_string) VALUES ($placeholders_string)";

            // * Execute the query
            $this->_execute($sql, $values);
        }
    }

    /**
     * Execute the query
     *
     * @param string $query
     * @param array $values
     * @return void
     **/
    private function _execute(string $query, array $values): void
    {
        $db = Outsider::get_connection();

        $statement = $db->prepare($query);
        $statement->execute($values);
    }

    public function count(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get a new factory instance for the model.
     *
     * @param string $model
     * @return \Abyss\Outsider\Factory
     */
    public static function factory_for_model($model): self
    {
        $model_name = str_replace(
            "Model",
            "",
            (new \ReflectionClass($model))->getShortName()
        );

        // Dynamically determine the factory class based on the model name
        $factory_class =
            "\\App\\Database\\Factories\\" . $model_name . "Factory";

        if (!class_exists($factory_class)) {
            throw new \Exception("Factory for model {$model} not found.");
        }

        $factory = new $factory_class();
        $factory->set_table_name_from_model($model_name);

        return $factory;
    }

    /**
     * Set the table name dynamically based on the model
     *
     * @param string $model
     */
    public function set_table_name_from_model($model): void
    {
        $model_name = strtolower($model);

        $this->table_name = $model_name . "s"; // Assuming table names are plural
    }

    /**
     * Define the default state of the model.
     *
     * @return array
     */
    abstract public function definition(): array;
}
