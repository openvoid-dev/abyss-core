<?php

namespace Abyss\Helpers;

use Doctrine\Inflector\InflectorFactory;

class StrHelper
{
    /**
     * Create a random string
     *
     * @param int $length
     * @return string
     **/
    public static function random(int $length = 10): string
    {
        $characters =
            "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $random_string = "";

        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $random_string .= $characters[$index];
        }

        return $random_string;
    }

    /**
     * Singularize a given word
     *
     * @param string $word
     * @return string
     **/
    public static function singularize(string $word): string
    {
        // * Build the inflector:
        $inflector = InflectorFactory::create()->build();

        // * Return singularized word
        return $inflector->singularize($word);
    }
}
