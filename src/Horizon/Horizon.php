<?php
/**
 * Simple controller based router
 */

namespace Abyss\Horizon;

use Abyss\Horizon\Middleware\Middleware;

class Horizon
{
    protected $routes = [];

    public function add($method, $uri, $controller) : static
    {
        $this->routes[] = [
            "uri"        => $uri,
            "controller" => $controller,
            "method"     => $method,
            "middleware" => null,
        ];

        return $this;
    }

    public function get(string $uri, $controller) : Horizon
    {
        return $this->add('GET', $uri, $controller);
    }

    public function post($uri, $controller) : Horizon
    {
        return $this->add('POST', $uri, $controller);
    }

    public function delete($uri, $controller) : Horizon
    {
        return $this->add('DELETE', $uri, $controller);
    }

    public function patch($uri, $controller) : Horizon
    {
        return $this->add('PATCH', $uri, $controller);
    }

    public function put($uri, $controller) : Horizon
    {
        return $this->add('PUT', $uri, $controller);
    }

    public function only($key) : static
    {
        $this->routes[array_key_last($this->routes)]['middleware'] = $key;

        return $this;
    }

    public function route() : mixed
    {
        $uri    = $this->get_uri();
        $method = $this->get_method();

        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === strtoupper($method)) {
                Middleware::resolve($route['middleware']);

                return require $this->base_path("controllers/" . $route['controller']);
            }
        }

        $this->abort();
    }

    public function previousUrl() : string
    {
        return $_SERVER['HTTP_REFERER'];
    }

    public function redirect(string $path) : never
    {
        header("location: {$path}");
        exit();
    }

    protected function abort(int $code = 404) : never
    {
        http_response_code($code);

        require $this->base_path("views/{$code}.php");

        die();
    }

    protected function base_path(string $path) : string
    {
        return dirname(__DIR__) . '/../app/' . $path;
    }

    protected function get_uri() : mixed
    {
        return parse_url($_SERVER['REQUEST_URI'])['path'];
    }

    protected function get_method() : mixed
    {
        return $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
    }
}
