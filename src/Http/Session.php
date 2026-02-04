<?php
namespace Mark\MjdCore\Http;

class Session
{
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['_flash'] = $_SESSION['_flash_next'] ?? [];

        $_SESSION['_flash_next'] = [];
    }

    public static function destroy()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_destroy();
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function flash($key, $message)
    {
        $_SESSION['_flash_next'][$key] = $message;
    }

    public static function unflash()
    {
        unset($_SESSION['_flash']);
    }

    public static function hasFlash($key)
    {
        return isset($_SESSION['_flash'][$key]);
    }

    public static function getFlash($key)
    {
        return $_SESSION['_flash'][$key] ?? null;
    }
}