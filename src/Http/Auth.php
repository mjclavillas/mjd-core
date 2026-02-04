<?php
namespace Mark\MjdCore\Http;

use App\Models\User;

class Auth
{
    public static function user()
    {
        $id = Session::get('user_id');
        if (!$id) return null;

        return (new User())->query()->where('id', $id)->first();
    }

    public static function check()
    {
        return Session::get('user_id') !== null;
    }

    public static function id()
    {
        return Session::get('user_id');
    }
}