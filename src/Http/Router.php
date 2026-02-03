<?php
namespace Mark\MjdCore\Http;

class Router {
    protected $routes = [];

    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }

    public function run() {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        $callback = $this->routes[$method][$path] ?? null;

        if (!$callback) {
            header("HTTP/1.0 404 Not Found");
            echo "404 - Not Found";
            return;
        }

        if (is_array($callback)) {
            [$class, $methodName] = $callback;

            if (class_exists($class)) {
                $controller = new $class();
                if (method_exists($controller, $methodName)) {
                    return call_user_func([$controller, $methodName]);
                }
            }
        }

        return call_user_func($callback);
    }

}