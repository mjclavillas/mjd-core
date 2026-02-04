<?php

namespace Mark\MjdCore\Database;

use PDO;

class Migrator
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function run()
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;");

        $files = glob(dirname(__DIR__, 2) . '/database/migrations/*.php');

        foreach ($files as $file) {
            $name = basename($file, '.php');

            $stmt = $this->db->prepare("SELECT id FROM migrations WHERE migration = ?");
            $stmt->execute([$name]);

            if (!$stmt->fetch()) {
                require_once $file;

                $class = str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', substr($name, 5))));

                $instance = new $class($this->db);
                echo "Applying migration: $name..." . PHP_EOL;
                $instance->up();

                $this->db->prepare("INSERT INTO migrations (migration) VALUES (?)")->execute([$name]);
                echo "Success." . PHP_EOL;
            }
        }
    }
}