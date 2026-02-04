<?php
namespace Mark\MjdCore\Core;

class Logger
{
    protected static $logPath = __DIR__ . '/../../storage/logs/mjdc.log';

    public static function log($message, $level = 'INFO')
    {
        $directory = dirname(self::$logPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $formattedMessage = "[{$timestamp}] [{$level}]: {$message}" . PHP_EOL;

        file_put_contents(self::$logPath, $formattedMessage, FILE_APPEND);
    }

    public static function info($message) { self::log($message, 'INFO'); }
    public static function error($message) { self::log($message, 'ERROR'); }
    public static function warning($message) { self::log($message, 'WARNING'); }
}