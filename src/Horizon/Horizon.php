<?php

namespace Abyss\Horizon;

use Abyss\Core\Application;
use Abyss\Horizon\Middleware\Middleware;

/**
 * Simple controller-based router with dynamic route parameters
 */
class Horizon
{
    /**
     * All of the defined routes
     *
     * @var array
     */
    protected static $routes = [];

    /**
     * Start the router and listen for requests
     *
     * @return void|Horizon
     */
    public static function start()
    {
        require Application::get_base_path('/server/routes/web.php');

        try {
            self::route();
        } catch (err) {
            return self::redirect(self::previousUrl());
        }
    }

    /**
     * Add a route to the routes array
     *
     * @param mixed $method
     * @param mixed $uri
     * @param mixed $controller
     * @return void
     */
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

    /**
     * Method for setting routes for GET requests
     *
     * @param string $uri
     * @param mixed $controller
     * @return void
     */
    public static function get(string $uri, $controller)
    {
        return self::add('GET', $uri, $controller);
    }

    /**
     * Method for setting routes for POST requests
     *
     * @param mixed $uri
     * @param mixed $controller
     * @return void
     */
    public static function post($uri, $controller)
    {
        return self::add('POST', $uri, $controller);
    }

    /**
     * Method for setting routes for DELETE requests
     *
     * @param mixed $uri
     * @param mixed $controller
     * @return void
     */
    public static function delete($uri, $controller)
    {
        return self::add('DELETE', $uri, $controller);
    }

    /**
     * Method for setting routes for PATCH requests
     *
     * @param mixed $uri
     * @param mixed $controller
     * @return void
     */
    public static function patch($uri, $controller)
    {
        return self::add('PATCH', $uri, $controller);
    }

    /**
     * Method for setting routes for PUT requests
     *
     * @param mixed $uri
     * @param mixed $controller
     * @return void
     */
    public static function put($uri, $controller)
    {
        return self::add('PUT', $uri, $controller);
    }

    /**
     * Method for defining middleware to a route
     *
     * @param mixed $key
     * @return void
     */
    public static function only($key)
    {
        self::$routes[array_key_last(self::$routes)]['middleware'] = $key;
    }

    /**
     * Handle the current request
     *
     * @return mixed
     */
    public static function route() : mixed
    {
        $uri    = self::get_uri();
        $method = self::get_method();

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

        self::abort();
    }

    /**
     * Get previous URL
     *
     * @return string
     */
    public static function previousUrl() : string
    {
        return $_SERVER['HTTP_REFERER'];
    }

    /**
     * Redirect to the defined page
     *
     * @param string $path
     * @return never
     */
    public static function redirect(string $path) : never
    {
        header("location: {$path}");
        exit();
    }

    /**
     * Abort and render a page corresponding to the error code
     *
     * @param int $code
     * @return never
     */
    protected static function abort(int $code = 404) : never
    {
        http_response_code($code);

        require Application::get_base_path("/app/views/{$code}.php");

        die();
    }

    /**
     * Get current URI
     *
     * @return mixed
     */
    protected static function get_uri() : mixed
    {
        return parse_url($_SERVER['REQUEST_URI'])['path'];
    }

    /**
     * Get request method
     *
     * @return mixed
     */
    protected static function get_method() : mixed
    {
        return $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
    }
}
