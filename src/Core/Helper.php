<?php

namespace Abyss\Core;

use Abyss\Horizon\Session;

/**
 * Helper methods
 */
class Helper
{
    /**
     * Summary of dd
     * @param mixed $value
     * @return never
     */
    public static function dd($value) : never
    {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";

        die();
    }

    /**
     * Summary of url_is
     * @param string $value
     * @return bool
     */
    public static function url_is(string $value) : bool
    {
        return $_SERVER['REQUEST_URI'] === $value;
    }

    /**
     * Summary of abort
     * @param int $code
     * @return never
     */
    public static function abort(int $code = 404) : never
    {
        http_response_code($code);

        require static::base_path("views/{$code}.php");

        die();
    }

    /**
     * Summary of authorize
     * @param mixed $condition
     * @param mixed $status
     * @return bool
     */
    public static function authorize($condition, $status = Response::FORBIDDEN) : bool
    {
        if (! $condition) {
            static::abort($status);
        }

        return true;
    }

    /**
     * Summary of base_path
     * @param string $path
     * @return string
     */
    public static function base_path(string $path) : string
    {
        return BASE_PATH . $path;
    }

    /**
     * Summary of view
     * @param string $path
     * @param array $attributes
     * @return void
     */
    public static function view(string $path, array $attributes = []) : void
    {
        extract($attributes);

        require static::base_path('views/' . $path);
    }

    /**
     * Summary of redirect
     * @param string $path
     * @return never
     */
    public static function redirect(string $path) : never
    {
        header("location: {$path}");
        exit();
    }

    /**
     * Summary of old
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public static function old($key, $default = '')
    {
        return Session::get('old')[$key] ?? $default;
    }

    public static function env(string $key, mixed $default_value = "")
    {
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        $value = getenv($key);

        return $value !== false ? $value : $default_value;
    }
}

