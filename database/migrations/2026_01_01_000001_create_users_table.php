<?php

use Mark\MjdCore\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(20) DEFAULT 'user',
                status VARCHAR(20) DEFAULT 'active',
                avatar VARCHAR(255) NULL,
                verification_token VARCHAR(255) NULL,
                is_verified TINYINT(1) DEFAULT 0,
                remember_token VARCHAR(255) NULL,
                deleted_at TIMESTAMP NULL DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB;
        ");
    }

    public function down()
    {
        $this->execute("DROP TABLE IF EXISTS users;");
    }
};