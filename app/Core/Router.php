<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    // The subfolder path — matches where your app lives in htdocs
    // e.g. if URL is localhost/expense_tracker/public/login
    // we strip /expense_tracker/public so the route becomes /login
    private string $basePath = '/expense_tracker/public';

    public function get(string $path, string $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, string $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        // Strip query string
        $path = strtok($uri, '?');

        // Strip the subfolder prefix so /expense_tracker/public/login becomes /login
        if ($this->basePath && str_starts_with($path, $this->basePath)) {
            $path = substr($path, strlen($this->basePath));
        }

        // Ensure leading slash
        if (empty($path)) {
            $path = '/';
        }
        

        $handler = $this->routes[$method][$path] ?? null;

        if (!$handler) {
            http_response_code(404);
            require __DIR__ . '/../Views/layouts/error.php';
            return;
        }

        [$controllerName, $methodName] = explode('@', $handler);
        $class = "App\\Controllers\\$controllerName";

        $controller = new $class();
        $controller->$methodName();
    }
}
