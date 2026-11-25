<?php
namespace App\Core;

/** Простой маршрутизатор: "METHOD /path/{id}" => [Controller::class, 'method']. */
class Router
{
    private array $routes = [];

    public function add(string $method, string $path, array $handler): void
    {
        $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $path) . '$#';
        $this->routes[] = [$method, $regex, $handler];
    }

    /** Находит маршрут и вызывает контроллер. POST-запросы проходят проверку CSRF. */
    public function dispatch(string $method, string $uri): void
    {
        $path = rtrim(parse_url($uri, PHP_URL_PATH) ?: '/', '/') ?: '/';
        foreach ($this->routes as [$m, $regex, $handler]) {
            if ($m === $method && preg_match($regex, $path, $match)) {
                if ($method === 'POST') {
                    Csrf::verify();
                }
                $args = array_filter($match, 'is_string', ARRAY_FILTER_USE_KEY);
                [$class, $action] = $handler;
                (new $class())->$action(...array_values($args));
                return;
            }
        }
        http_response_code(404);
        View::render('error', ['code' => 404, 'message' => 'Страница не найдена.']);
    }
}
