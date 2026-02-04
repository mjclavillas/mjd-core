<?php
namespace App\Middleware;

use Mark\MjdCore\Http\Middleware;
use Mark\MjdCore\Http\Csrf;
use Mark\MjdCore\Http\Request;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        '/api/webhook',
        '/external-callback'
    ];

    public function handle(Request $request, callable $next)
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (str_starts_with($path, '/api') || in_array($path, $this->except)) {
            return $next($request);
        }

        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
            return $next($request);
        }

        $token = $request->input('_csrf') ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (!Csrf::verify($token)) {
            http_response_code(419);
            die("419 - CSRF Token Mismatch or Expired");
        }

        return $next($request);
    }
}