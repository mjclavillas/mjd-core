<?php
namespace Mark\MjdCore\Http;

class Request
{
    protected array $data;

    public function __construct()
    {
        $this->data = array_merge($_GET, $_POST);
    }

    public function input(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->data;
    }

    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function path(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        return $path;
    }
}