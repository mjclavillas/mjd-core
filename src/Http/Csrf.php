<?php
namespace Mark\MjdCore\Http;

class Csrf
{
    public static function generate(): string
    {
        if (!Session::get('_csrf_token')) {
            Session::set('_csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('_csrf_token');
    }

    public static function verify(string $token): bool
    {
        $stored = Session::get('_csrf_token');
        return $stored && hash_equals($stored, $token);
    }
}