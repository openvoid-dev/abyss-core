<?php

/**
 * Simple controller-based router with dynamic route parameters
 *
 */

namespace Abyss\Horizon;

use Abyss\Core\Application;
use Abyss\Horizon\Middleware\Middleware;
use Closure;
use Exception;

class Horizon
{
    /**
     * All the defined routes
     *
     * @var array
     */
    protected static array $routes = [];

    /**
     * Start the router and listen for requests
     *
     * @return void
     */
    public static function start(): void
    {
        require Application::get_base_path("/app/routes/web.php");

        try {
            self::route();
        } catch (Exception) {
            self::redirect(self::previousUrl());
        }
    }

    /**
     * Add a route to the routes array
     *
     * @param mixed $method
     * @param mixed $uri
     * @param Closure|array $action
     * @return void
     */
    public static function add(mixed $method, mixed $uri, Closure|array $action): void
    {
        // * Convert dynamic route placeholders {test_slug} to regex for matching
        $uri = preg_replace(
            "/\{([a-zA-Z_][a-zA-Z0-9_-]*)\}/",
            '(?P<\1>[^/]+)',
            $uri
        );

        // * Allow for optional trailing slash by the end of the url
        $uri = rtrim($uri, "/") . "/?";

        self::$routes[] = [
            "uri"        => $uri,
            "method"     => $method,
            "middleware" => null,
            "action"     => $action,
        ];
    }

    /**
     * Method for setting routes for GET requests
     *
     * @param string $uri
     * @param Closure|array $action
     * @return void
     */
    public static function get(string $uri, Closure|array $action): void
    {
        self::add("GET", $uri, $action);
    }

    /**
     * Method for setting routes for POST requests
     *
     * @param mixed $uri
     * @param Closure|array $action
     * @return void
     */
    public static function post(string $uri, Closure|array $action): void
    {
        self::add("POST", $uri, $action);
    }

    /**
     * Method for setting routes for DELETE requests
     *
     * @param mixed $uri
     * @param Closure|array $action
     * @return void
     */
    public static function delete(string $uri, Closure|array $action): void
    {
        self::add("DELETE", $uri, $action);
    }

    /**
     * Method for setting routes for PATCH requests
     *
     * @param mixed $uri
     * @param Closure|array $action
     * @return void
     */
    public static function patch(string $uri, Closure|array $action): void
    {
        self::add("PATCH", $uri, $action);
    }

    /**
     * Method for setting routes for PUT requests
     *
     * @param mixed $uri
     * @param Closure|array $action
     * @return void
     */
    public static function put(string $uri, Closure|array $action): void
    {
        self::add("PUT", $uri, $action);
    }

    /**
     * Method for defining middleware to a route
     *
     * @param mixed $key
     * @return void
     */
    public static function only(mixed $key): void
    {
        self::$routes[array_key_last(self::$routes)]["middleware"] = $key;
    }

    /**
     * Handle the current request
     *
     * @return mixed
     * @throws Exception
     */
    public static function route(): mixed
    {
        $uri    = self::get_uri();
        $method = self::get_method();

        foreach (self::$routes as $route) {
            // * Use regex to match dynamic URI patterns like /tests/{test_slug}
            $pattern = "@^" . $route["uri"] . "$@";

            if (
                !preg_match($pattern, $uri, $matches) ||
                $route["method"] !== strtoupper($method)
            ) {
                continue;
            }

            Middleware::resolve($route["middleware"]);

            $params = array_filter($matches, "is_string", ARRAY_FILTER_USE_KEY);

            // * If actions is not a closure, it means it's an array
            // * consisting of controller and method
            if (!$route["action"] instanceof Closure) {
                return call_user_func_array($route["action"], $params);
            }

            // * Call closure and pass $params from the url
            echo $route["action"]($params);

            break;
        }

        self::abort();
    }

    /**
     * Get previous URL
     *
     * @return string
     */
    public static function previousUrl(): string
    {
        return $_SERVER["HTTP_REFERER"];
    }

    /**
     * Redirect to the defined page
     *
     * @param string $path
     * @return never
     */
    public static function redirect(string $path): never
    {
        header("location: $path");
        exit();
    }

    /**
     * Abort and render a page corresponding to the error code
     *
     * @param int $code
     * @return never
     */
    protected static function abort(int $code = 404): never
    {
        http_response_code($code);

        require Application::get_base_path("/app/views/$code.php");

        die();
    }

    /**
     * Get current URI
     *
     * @return mixed
     */
    protected static function get_uri(): mixed
    {
        return parse_url($_SERVER["REQUEST_URI"])["path"];
    }

    /**
     * Get request method
     *
     * @return mixed
     */
    protected static function get_method(): mixed
    {
        return $_POST["_method"] ?? $_SERVER["REQUEST_METHOD"];
    }
}
