<?php

use Mark\MjdCore\Http\Router;
use App\Controllers\HomeController;

$router = new Router();

$router->get('/', function() {
    echo "Welcome to MJD-Core Framework!";
});

$router->get('/', [HomeController::class, 'index']);

$router->run();