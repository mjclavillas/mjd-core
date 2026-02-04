<?php
namespace Mark\MjdCore\Http\View;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;
use Mark\MjdCore\Http\Csrf;
use Mark\MjdCore\Http\Session;
use App\Models\User;
use Mark\MjdCore\Http\Request;
use Mark\MjdCore\Http\Router;

class TwigExtension extends AbstractExtension implements GlobalsInterface
{
    protected $router;
    protected $request;
    protected $executionTime;

    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->request = new Request();
        $this->executionTime = number_format(microtime(true) - APP_START, 3);
    }

    public function getGlobals(): array
    {
        return [
            'app_name' => $_ENV['APP_NAME'] ?? 'MJD-Core',
            'session'  => $_SESSION,
            'execution_time' => $this->executionTime,
            'current_path' => $this->request->path(),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('csrf_field', [$this, 'csrfField'], ['is_safe' => ['html']]),
            new TwigFunction('auth_check', [$this, 'authCheck']),
            new TwigFunction('auth_user', [$this, 'authUser']),
            new TwigFunction('flash', [Session::class, 'getFlash']),
            new TwigFunction('has_flash', [Session::class, 'hasFlash']),
            new TwigFunction('is_route', [$this, 'isRoute']),
        ];
    }

    public function authCheck(): bool
    {
        return Session::get('user_id') !== null;
    }

    public function authUser()
    {
        if (!$this->authCheck()) {
            return null;
        }

        $userId = Session::get('user_id');

        return User::where('id', $userId)->first();
    }

    public function csrfField(): string
    {
        $token = Csrf::generate();
        return '<input type="hidden" name="_csrf" value="' . $token . '">';
    }

    public function isRoute(string $identity): bool
    {
        $current = $this->router->getCurrentRoute();

        if (empty($current)) {
            return false;
        }

        return ($current['name'] ?? null) === $identity || ($current['path'] ?? null) === $identity;
    }
}