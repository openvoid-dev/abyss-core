<?php

namespace Abyss\Outsider;

trait HasFactory
{
    /**
     * Get a new factory instance for the model.
     *
     * @param callable|array|int|null $count
     * @param callable|array $state
     * @return \Abyss\Outsider\Factory
     */
    public static function factory($count = null, $state = [])
    {
        // Get the factory dynamically for the model
        $factory = Factory::factory_for_model(get_called_class());

        // Set the count and state for the factory
        return $factory->count(is_numeric($count) ? $count : null);
        // ->state(is_callable($count) || is_array($count) ? $count : $state);
    }
}
