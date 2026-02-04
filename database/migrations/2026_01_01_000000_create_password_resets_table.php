<?php

use Mark\MjdCore\Database\Migration;

class CreatePasswordResetsTable extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE password_resets (
                email VARCHAR(255) NOT NULL,
                token VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX (email),
                INDEX (token)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down()
    {
        $this->execute("DROP TABLE IF EXISTS password_resets;");
    }
}