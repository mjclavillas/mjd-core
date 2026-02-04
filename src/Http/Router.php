<?php
namespace Mark\MjdCore\Http;
use Mark\MjdCore\Http\View;
use \App\Middleware\VerifyCsrfToken;

class Router {
    protected $container;

    protected $routes = [];
    protected $namedRoutes = [];
    protected $groupAttributes = [];
    protected $globalMiddleware = [];
    protected $currentRoute = [];

    public function __construct($container) {
        $this->container = $container;
    }

    public function dispatch(Request $request)
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_-]+)', $route['path']);
            $pattern = "#^" . $pattern . "$#";

            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                $this->currentRoute = $route;

                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                return $this->execute($route['handler'], $request, $params);
            }
        }

        http_response_code(404);
        echo "404 - [SYSTEM_ERR]: Route not mapped to any identity.";
        exit;
    }

    protected function execute($handler, Request $request, array $params)
    {
        if (is_array($handler)) {
            [$controllerClass, $method] = $handler;

            $controller = new $controllerClass();

            return call_user_func_array([$controller, $method], array_merge([$request], $params));
        }

        if (is_callable($handler)) {
            return call_user_func_array($handler, array_merge([$request], $params));
        }
    }

    public function getCurrentRoute(): array
    {
        return $this->currentRoute;
    }

    public function use($middleware) {
        $this->globalMiddleware[] = VerifyCsrfToken::class;
        $this->globalMiddleware[] = $middleware;
    }

    public function group(array $attributes, callable $callback) {
        $previousAttributes = $this->groupAttributes;
        $this->groupAttributes = array_merge($previousAttributes, $attributes);

        $callback($this);

        $this->groupAttributes = $previousAttributes;
    }

    public function addRoute($method, $path, $callback, $middleware = []) {
        $prefix = $this->groupAttributes['prefix'] ?? '';
        $fullPath = $prefix . $path;

        $groupMiddleware = $this->groupAttributes['middleware'] ?? [];
        $allMiddleware = array_merge($groupMiddleware, $middleware);

        $this->routes[$method][$fullPath] = [
        'callback' => $callback,
        'middleware' => $allMiddleware
    ];

    return $this;
    }

    public function get($path, $callback, $middleware = []) {
        $this->addRoute('GET', $path, $callback, $middleware);
    }

    public function post($path, $callback, $middleware = []) {
        $this->addRoute('POST', $path, $callback, $middleware);
    }

    public function run() {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        if (isset($this->routes[$method][$path])) {
            $route = $this->routes[$method][$path];
        } else {
            foreach ($this->routes as $m => $paths) {
                if (isset($paths[$path])) {
                    return View::error(405);
                }
            }

            return View::error(404);
        }

        $callback = $route['callback'];
        $middlewares = array_merge($this->globalMiddleware, $route['middleware']);

        $coreAction = function() use ($callback) {
            if (is_array($callback)) {
                [$class, $method] = $callback;

                $controller = $this->container->make($class);

                $reflector = new \ReflectionMethod($class, $method);
                $parameters = $reflector->getParameters();
                $dependencies = [];

                foreach ($parameters as $parameter) {
                    $type = $parameter->getType();

                    if ($type && !$type->isBuiltin()) {
                        $dependencies[] = $this->container->make($type->getName());
                    } else {
                        $dependencies[] = $parameter->isDefaultValueAvailable()
                            ? $parameter->getDefaultValue()
                            : null;
                    }
                }

                return $controller->{$method}(...$dependencies);
            }

            return call_user_func($callback);
        };

        $pipeline = array_reduce(
            array_reverse($middlewares),
            function ($stack, $middlewareClass) {
                return function () use ($stack, $middlewareClass) {
                    return (new $middlewareClass())->handle($stack);
                };
            },
            $coreAction
        );

        return $pipeline();
    }

    public function name($name) {
        $method = array_key_last($this->routes);
        $path = array_key_last($this->routes[$method]);

        $this->namedRoutes[$name] = $path;
        return $this;
    }

    public function getUrl($name) {
        return $this->namedRoutes[$name] ?? '#';
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }
}