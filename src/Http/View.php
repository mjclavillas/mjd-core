<?php
namespace Mark\MjdCore\Http;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Mark\MjdCore\Http\View\TwigExtension;

class View
{
    protected static $twig;
    protected static $router;

    public static function init(Router $router)
    {
        self::$router = $router;
    }

    public static function render(string $view, array $data = [])
    {
        if (!self::$twig) {
            if (!self::$router) {
                throw new \Exception("[CORE_ERR]: View engine initialized before Router link was established.");
            }
            $loader = new FilesystemLoader(__DIR__ . '/../../views');

            self::$twig = new Environment($loader, [
                'cache' => false,
                'debug' => $_ENV['APP_DEBUG'] === 'true',
            ]);

            self::$twig->addExtension(new TwigExtension(self::$router));
        }

        $view = str_ends_with($view, '.twig') ? $view : $view . '.twig';

        $data['flash'] = $_SESSION['_flash'] ?? [];

        $data['env'] = $_ENV['APP_ENV'] ?? 'development';
        return self::$twig->render($view, $data);
    }


    public static function error($code) {
        $data = match($code) {
            401 => ['title' => 'Unauthorized', 'message' => 'Identity could not be verified.'],
            403 => ['title' => 'Forbidden', 'message' => 'Access levels insufficient for resource.'],
            405 => ['title' => 'Method Invalid', 'message' => 'Request method rejected by endpoint.'],
            500 => ['title' => 'Engine Failure', 'message' => 'Unexpected state in core runtime.'],
            default => ['title' => 'Unknown State', 'message' => 'An undefined error has occurred.']
        };

        $data['code'] = $code;
        http_response_code($code);
        echo self::render('errors/error', $data);
        exit;
    }
}