<?php
namespace Mark\MjdCore\Http;

abstract class Controller
{
    protected function view(string $name, array $data = [])
    {
        echo View::render($name, $data);
    }
}