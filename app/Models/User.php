<?php
namespace App\Models;

use Mark\MjdCore\Database\Model;

class User extends Model
{
    protected $table = 'users';

    protected function create(array $data)
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        $this->attributes = $data;
        $this->save();

        return $this;
    }

    protected function verify($email, $password)
    {
        $user = $this->query()->where('email', $email)->first();

        if ($user && password_verify($password, $user->attributes['password'])) {
            return $user;
        }
        return false;
    }
}