<?php

namespace Mark\MjdCore\Database;

use PDO;

abstract class Migration
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    protected function execute($sql)
    {
        return $this->db->exec($sql);
    }

    abstract public function up();

    abstract public function down();
}