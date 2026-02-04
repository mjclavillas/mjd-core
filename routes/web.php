<?php
use App\Middleware\AuthMiddleware;
use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\RegisterController;
use App\Controllers\PasswordResetController;

$router->get('/', [HomeController::class, 'index']);
$router->get('/docs', [HomeController::class, 'documentation']);

$router->get('/login', [LoginController::class, 'show']);
$router->post('/auth/login', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

$router->get('/register', [RegisterController::class, 'show']);
$router->post('/auth/register', [RegisterController::class, 'store']);
$router->get('/verify-email', [RegisterController::class, 'verify']);

$router->get('/forgot-password', [PasswordResetController::class, 'showRequestForm']);
$router->post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
$router->get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm']);
$router->post('/reset-password', [PasswordResetController::class, 'updatePassword']);

$router->group(['middleware' => [\App\Middleware\AuthenticateMiddleware::class]], function($router) {
    $router->get('/dashboard', [App\Controllers\DashboardController::class, 'index']);
});