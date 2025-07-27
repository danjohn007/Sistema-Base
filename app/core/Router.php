<?php

class Router {
    private $routes = [];

    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }

    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $this->getCurrentPath();

        if (!isset($this->routes[$method][$path])) {
            http_response_code(404);
            echo "404 - Page Not Found";
            return;
        }

        $callback = $this->routes[$method][$path];

        if (is_callable($callback)) {
            call_user_func($callback);
        } elseif (is_string($callback)) {
            $this->callControllerAction($callback);
        }
    }

    private function getCurrentPath() {
        $path = $_SERVER['REQUEST_URI'];
        $path = parse_url($path, PHP_URL_PATH);
        
        // Remove base path if running in subdirectory
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ($basePath !== '/') {
            $path = substr($path, strlen($basePath));
        }
        
        return $path ?: '/';
    }

    private function callControllerAction($callback) {
        list($controller, $action) = explode('@', $callback);
        
        $controllerFile = __DIR__ . "/../controllers/{$controller}.php";
        
        if (!file_exists($controllerFile)) {
            throw new Exception("Controller not found: {$controller}");
        }

        require_once $controllerFile;
        
        if (!class_exists($controller)) {
            throw new Exception("Controller class not found: {$controller}");
        }

        $controllerInstance = new $controller();
        
        if (!method_exists($controllerInstance, $action)) {
            throw new Exception("Action not found: {$action} in {$controller}");
        }

        call_user_func([$controllerInstance, $action]);
    }
}