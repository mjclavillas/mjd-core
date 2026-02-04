<?php

namespace Mark\MjdCore\Http;

use Mark\MjdCore\Http\View;

abstract class Controller
{
    protected function view(string $template, array $data = [])
    {
        $globalData = [
            'session' => $_SESSION,
            'flash'   => $this->getFlashMessages(),
            'auth'    => Session::get('user'),
            'env'     => $_ENV['APP_ENV'] ?? 'production'
        ];

        return View::render($template, array_merge($globalData, $data));
    }

    private function getFlashMessages(): array
    {
        return $_SESSION['_flash_prev'] ?? [];
    }
}