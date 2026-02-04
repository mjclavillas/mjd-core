<?php
namespace Mark\MjdCore\Console;

class Kernel
{
    protected $tasks = [];

    public function add(string $name, callable $action, string $frequency)
    {
        $this->tasks[] = [
            'name'      => $name,
            'action'    => $action,
            'frequency' => $frequency
        ];
    }

    public function run()
    {
        foreach ($this->tasks as $task) {
            if ($this->isDue($task['frequency'])) {
                echo "Running task: {$task['name']}...\n";
                call_user_func($task['action']);
            }
        }
    }

    protected function isDue($frequency)
    {
        $minute = date('i');
        $hour   = date('H');

        return match ($frequency) {
            'everyMinute' => true,
            'hourly'      => $minute === '00',
            'daily'       => $hour === '00' && $minute === '00',
            default       => false,
        };
    }
}