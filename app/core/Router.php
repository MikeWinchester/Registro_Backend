<?php
class Router {
    private $routes = [];
    private $basePath = '/'; // Ruta base de la aplicación (ajustar según sea necesario)

    public function addRoute($method, $path, $controller, $action) {
        // Asegúrate de que las rutas sean relativas
        if ($path[0] === '/') {
            $path = substr($path, 1); // Eliminar la barra inicial
        }
        $this->routes[$method][$path] = ["controller" => $controller, "action" => $action];
    }

    public function dispatch($method, $uri) {
        // Elimina los parámetros GET de la URI
        $uri = parse_url($uri, PHP_URL_PATH);
        
        // Elimina la ruta base de la URI si existe
        $uri = ltrim($uri, '/'); // Eliminar la barra inicial
        
        foreach ($this->routes[$method] as $route => $handler) {
            // Convierte {param} en regex para detectar variables dinámicas
            $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $route);

            if (preg_match("#^$pattern$#", $uri, $matches)) {
                array_shift($matches); // Eliminar el primer elemento (el nombre de la ruta)
                $controller = new $handler["controller"]();
                call_user_func_array([$controller, $handler["action"]], $matches);
                return;
            }
        }

        // Si no se encuentra la ruta, retornar 404
        http_response_code(404);
        echo json_encode(["error" => "Ruta no encontrada"]);
    }
}
?>
