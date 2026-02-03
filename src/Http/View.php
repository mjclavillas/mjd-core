<?php
namespace Mark\MjdCore\Http;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class View
{
    protected static $twig;

    public static function render(string $view, array $data = []) 
    {
        if (!self::$twig) {
            $loader = new FilesystemLoader(__DIR__ . '/../../views');
            
            self::$twig = new Environment($loader, [
                'cache' => false,
                'debug' => $_ENV['APP_DEBUG'] === 'true',
            ]);
        }

        $view = str_ends_with($view, '.twig') ? $view : $view . '.twig';

        return self::$twig->render($view, $data);
    }
}