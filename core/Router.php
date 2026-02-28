<?php

declare(strict_types=1);

namespace Core;

final class Router
{
    private array $routes = [];

    public function get(string $path, string $action): void
    {
        $this->routes['GET'][$path] = $action;
    }

    public function post(string $path, string $action): void
    {
        $this->routes['POST'][$path] = $action;
    }

    public function put(string $path, string $action): void
    {
        $this->routes['PUT'][$path] = $action;
    }

    public function patch(string $path, string $action): void
    {
        $this->routes['PATCH'][$path] = $action;
    }

    public function delete(string $path, string $action): void
    {
        $this->routes['DELETE'][$path] = $action;
    }

    public function dispatch(string $method, string $uri): void
    {
        $valid_method = strtoupper($method);
        $parsed_uri = parse_url($uri, PHP_URL_PATH);

        foreach ($this->routes[$valid_method] as $pattern => $actionPath) {
            $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';

            if (preg_match($regex, $parsed_uri, $matches)) {
                [$controllerClass, $action] = explode("@", $actionPath);

                $params = array_filter($matches, "is_string", ARRAY_FILTER_USE_KEY);

                if (!class_exists($controllerClass)) {
                    throw new \Exception('Controller not found' . $controllerClass);
                }

                $controller = new $controllerClass();

                if (!method_exists($controller, $action)) {
                    throw new \Exception('Action not found' . $action);
                }

                echo call_user_func_array([$controller, $action], $params);
                return;
            }
        }

        http_response_code(404);
        echo "Not found" . " " . $uri;
    }
}
