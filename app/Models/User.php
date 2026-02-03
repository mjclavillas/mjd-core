<?php
namespace App\Models;

use Mark\MjdCore\Database\Model;

class User extends Model
{
    protected $table = 'users';

    public function getActiveUsers()
    {
        return $this->db->query("SELECT * FROM users WHERE status = 'active'")->fetchAll();
    }
}