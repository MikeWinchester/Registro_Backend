<?php
class Router {
    private $routes = [];

    public function addRoute($method, $path, $controller, $action) {
        $this->routes[$method][$path] = ["controller" => $controller, "action" => $action];
    }

    public function dispatch($method, $uri) {
        $uri = parse_url($uri, PHP_URL_PATH); // Limpia parámetros GET

        foreach ($this->routes[$method] as $route => $handler) {
            // Convierte {param} en regex para detectar variables dinámicas
            $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $route);

            if (preg_match("#^$pattern$#", $uri, $matches)) {
                array_shift($matches);
                $controller = new $handler["controller"]();
                call_user_func_array([$controller, $handler["action"]], $matches);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(["error" => "Ruta no encontrada"]);
    }
}
?>

