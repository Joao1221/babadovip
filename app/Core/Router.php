<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function get(string $uri, array|callable $handler): void
    {
        $this->add('GET', $uri, $handler);
    }

    public function post(string $uri, array|callable $handler): void
    {
        $this->add('POST', $uri, $handler);
    }

    private function add(string $method, string $uri, array|callable $handler): void
    {
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $uri);
        $pattern = '#^' . rtrim($pattern, '/') . '/?$#';
        $this->routes[$method][] = ['pattern' => $pattern, 'handler' => $handler];
    }

    public function dispatch(string $method, string $uri): void
    {
        $methodRoutes = $this->routes[$method] ?? [];
        $cleanUri = '/' . trim($uri, '/');
        if ($cleanUri === '//') {
            $cleanUri = '/';
        }

        foreach ($methodRoutes as $route) {
            if (!preg_match($route['pattern'], $cleanUri, $matches)) {
                continue;
            }

            $params = array_filter($matches, static fn($key): bool => is_string($key), ARRAY_FILTER_USE_KEY);
            $handler = $route['handler'];
            if (is_callable($handler)) {
                call_user_func_array($handler, $params);
                return;
            }
            [$class, $action] = $handler;
            $controller = new $class();
            call_user_func_array([$controller, $action], $params);
            return;
        }

        http_response_code(404);
        echo View::render('errors/404');
    }
}
