<?php
use Mark\MjdCore\Core\Container;
use Mark\MjdCore\Http\Router;
use Mark\MjdCore\Database\DB;
use Mark\MjdCore\Core\ExceptionHandler;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$container = new Container();

/* $container->singleton(DB::class, function() {
    return DB::getInstance();
}); */

$container->singleton(Router::class, function($c) {
    $router = new Router($c);
    Mark\MjdCore\Http\View::init($router);
    return $router;
});

ExceptionHandler::register();

return $container;