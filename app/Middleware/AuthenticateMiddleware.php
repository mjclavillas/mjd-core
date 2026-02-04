<?php

namespace App\Middleware;

use Mark\MjdCore\Http\Middleware;
use Mark\MjdCore\Http\Session;

class AuthenticateMiddleware extends Middleware
{
    public function handle(callable $next)
    {
        if (!Session::get('user_id')) {
            Session::flash('error', 'You must be logged in to access this page.');
            return $this->redirect('/login');
        }

        return $next();
    }
}
