<?php
namespace Mark\MjdCore\Http;

abstract class Middleware
{
    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }

    abstract public function handle(callable $next);
}