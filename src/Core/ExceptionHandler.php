<?php
namespace Mark\MjdCore\Core;

use Throwable;
use Mark\MjdCore\Http\View;

class ExceptionHandler
{
    public static function register()
    {
        set_exception_handler([self::class, 'handleException']);

        set_error_handler(function($level, $message, $file, $line) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        });
    }

    public static function handleException(Throwable $e)
    {
        http_response_code(500);
        $isApi = str_starts_with($_SERVER['REQUEST_URI'], '/api') ||
            ($_SERVER['HTTP_ACCEPT'] ?? '') === 'application/json';

        if ($isApi) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'error' => true,
                'message' => $e->getMessage(),
                'trace' => $_ENV['APP_DEBUG'] === 'true' ? $e->getTrace() : []
            ]);
            exit;
        }

        Logger::error($e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());

        if (App::isProduction()) {
            error_log($e->getMessage());
            return View::error(405);/* , [
                'env' => $_ENV['APP_ENV'] ?? 'development',
                'exception' => $e,
                'message' => 'The server encountered an internal error and was unable to complete your request.'
            ]); */
            exit;
        } else {
            self::renderDebug($e);
        }
        exit;
    }

    private static function renderDebug(Throwable $e)
    {
        echo "<h1>Unhandled Exception</h1>";
        echo "<p><strong>Message:</strong> {$e->getMessage()}</p>";
        echo "<p><strong>File:</strong> {$e->getFile()} on line {$e->getLine()}</p>";
        echo "<pre>{$e->getTraceAsString()}</pre>";
    }
}