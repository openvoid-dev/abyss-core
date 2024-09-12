<?php

/**
 * Simple controller-based router with dynamic route parameters
 */

namespace Abyss\Horizon;

use Abyss\Core\Application;
use Abyss\Horizon\Middleware\Middleware;

class Horizon
{
    protected static $routes = [];

    public static function start()
    {
        require Application::get_base_path('/server/routes/web.php');

        try {
            static::route();
        } catch (err) {
            return static::redirect(static::previousUrl());
        }
    }

    public static function add($method, $uri, $controller)
    {
        // * Convert dynamic route placeholders {test_slug} to regex for matching
        $uri = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_-]*)\}/', '(?P<\1>[^/]+)', $uri);

        // * Allow for optional trailing slash by the end of the url
        $uri = rtrim($uri, '/') . '/?';

        self::$routes[] = [
            "uri"               => $uri,
            "controller"        => $controller[0],
            "controller_method" => $controller[1],
            "method"            => $method,
            "middleware"        => null,
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
        self::$routes[array_key_last(static::$routes)]['middleware'] = $key;
    }

    public static function route() : mixed
    {
        $uri    = static::get_uri();
        $method = static::get_method();

        foreach (self::$routes as $route) {
            // * Use regex to match dynamic URI patterns like /tests/{test_slug}
            $pattern = "@^" . $route['uri'] . "$@";

            if (! preg_match($pattern, $uri, $matches) || $route['method'] !== strtoupper($method)) {
                continue;
            }

            Middleware::resolve($route['middleware']);

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            return call_user_func_array([$route["controller"], $route["controller_method"]], $params);
        }

        static::abort();
    }

    public static function previousUrl() : string
    {
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
