<?php
namespace Mark\MjdCore\Http;

abstract class Controller
{
    protected function view(string $name, array $data = [])
    {
        echo View::render($name, $data);
    }

    protected function json(array $data, int $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    protected function redirect($url) {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        header("Location: {$url}");
        return "";
    }
}