<?php
use Mark\MjdCore\Http\Session;
use Mark\MjdCore\Http\Router;
use Mark\MjdCore\Http\View;

define('APP_START', microtime(true));

require_once __DIR__ . '/../vendor/autoload.php';

Session::start();

$container = require_once __DIR__ . '/../bootstrap/app.php';

$router = $container->make(Router::class);

require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/api.php';

$router->run();

Session::unflash();