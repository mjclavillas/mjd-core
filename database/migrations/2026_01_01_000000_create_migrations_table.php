<?php

use Mark\MjdCore\Database\Migration;

class CreateMigrationsTable extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB;
        ");
    }

    public function down()
    {
        $this->execute("DROP TABLE IF EXISTS migrations;");
    }
};