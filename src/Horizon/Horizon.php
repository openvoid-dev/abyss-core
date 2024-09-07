<?php
/**
 * Simple controller based router
 */

namespace Abyss\Horizon;

use Abyss\Horizon\Middleware\Middleware;

class Horizon
{
    protected static $routes = [];

    public static function add($method, $uri, $controller)
    {
        self::$routes[] = [
            "uri"        => $uri,
            "controller" => $controller,
            "method"     => $method,
            "middleware" => null,
        ];
    }

    public static function get(string $uri, $controller)
    {
        return static::add('GET', $uri, $controller);
    }

    public static function post($uri, $controller)
    {
        return static::add('POST', $uri, $controller);
    }

    public static function delete($uri, $controller)
    {
        return static::add('DELETE', $uri, $controller);
    }

    public static function patch($uri, $controller)
    {
        return static::add('PATCH', $uri, $controller);
    }

    public static function put($uri, $controller)
    {
        return static::add('PUT', $uri, $controller);
    }

    public static function only($key)
    {
        self::$routes[array_key_last(static::routes)]['middleware'] = $key;
    }

    public static function route() : mixed
    {
        $uri    = static::get_uri();
        $method = static::get_method();

        foreach (self::$routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === strtoupper($method)) {
                Middleware::resolve($route['middleware']);

                return require static::base_path("controllers/" . $route['controller']);
            }
        }

        static::abort();
    }

    public static function previousUrl() : string
    {
        var_dump("aa");
        return $_SERVER['HTTP_REFERER'];
    }

    public static function redirect(string $path) : never
    {
        header("location: {$path}");
        exit();
    }

    protected static function abort(int $code = 404) : never
    {
        http_response_code($code);

        require static::base_path("views/{$code}.php");

        die();
    }

    protected static function base_path(string $path) : string
    {
        return dirname(__DIR__) . '/../app/' . $path;
    }

    protected static function get_uri() : mixed
    {
        return parse_url($_SERVER['REQUEST_URI'])['path'];
    }

    protected static function get_method() : mixed
    {
        return $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
    }
}
