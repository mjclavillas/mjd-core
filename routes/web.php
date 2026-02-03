<?php

use Mark\MjdCore\Http\Router;
use App\Controllers\HomeController;

$router = new Router();

$router->get('/', function() {
    echo "Hello world!";
});

$router->get('/', [HomeController::class, 'index']);

$router->run();