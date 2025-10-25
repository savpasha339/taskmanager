<?php
namespace App\Core;

class Router {
    private $routes = [];

    public function add($route, $controller, $method) {
        $this->routes[$route] = [
            'controller' => $controller,
            'method' => $method
        ];
    }

    public function dispatch($uri) {
        // Очищаем URI
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = trim($uri, '/');
        
        // Главная страница
        if (empty($uri)) {
            $uri = 'login';
        }

        // Ищем маршрут
        if (isset($this->routes[$uri])) {
            $route = $this->routes[$uri];
            $controllerName = "App\\Controllers\\" . $route['controller'];
            $methodName = $route['method'];

            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                if (method_exists($controller, $methodName)) {
                    return $controller->$methodName();
                }
            }
        }

        // 404
        http_response_code(404);
        $this->show404();
    }

    private function show404() {
        echo "<h1>404 - Страница не найдена</h1>";
        echo "<p>Запрашиваемая страница не существует.</p>";
        echo '<a href="/login">Вернуться на главную</a>';
    }
}
?>