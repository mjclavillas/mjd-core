<?php
namespace Mark\MjdCore\Core;

class App
{
    public static function environment(...$environments)
    {
        $currentEnv = $_ENV['APP_ENV'] ?? 'production';

        if (empty($environments)) {
            return $currentEnv;
        }

        return in_array($currentEnv, $environments);
    }

    public static function isLocal()
    {
        return self::environment('local', 'dev', 'development');
    }

    public static function isProduction()
    {
        return self::environment('production');
    }
}