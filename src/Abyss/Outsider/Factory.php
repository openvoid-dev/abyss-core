<?php

namespace Abyss\Outsider;

abstract class Factory
{
    protected int $count = 1;

    /**
     * Run the factory
     *
     *
     */
    public function create()
    {
        for ($i = 0; $i < $this->count; $i++) {
            var_dump($this->definition());
        }
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
    public static function factory_for_model($model)
    {
        $model_name = str_replace(
            "Model",
            "",
            (new \ReflectionClass($model))->getShortName()
        );

        // Dynamically determine the factory class based on the model name
        $factoryClass =
            "\\App\\Database\\Factories\\" . $model_name . "Factory";

        if (class_exists($factoryClass)) {
            return new $factoryClass();
        }

        throw new \Exception("Factory for model {$model} not found.");
    }

    /**
     * Define the default state of the model.
     *
     * @return array
     */
    abstract public function definition(): array;
}
