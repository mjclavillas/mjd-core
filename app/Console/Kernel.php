<?php
namespace App\Console;

use Mark\MjdCore\Console\Kernel as BaseKernel;
use Mark\MjdCore\Core\Logger;

class Kernel extends BaseKernel
{
    public function schedule()
    {
        $this->add('Clear Logs', function() {
            $logFile = __DIR__ . '/../../storage/logs/mjdc.log';
            if (file_exists($logFile)) unlink($logFile);
            Logger::info("Logs cleared by scheduled task.");
        }, 'daily');

        $this->add('Heartbeat', function() {
            Logger::info("System Heartbeat: Online");
        }, 'everyMinute');
    }
}