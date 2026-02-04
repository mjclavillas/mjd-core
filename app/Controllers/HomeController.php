<?php

namespace App\Controllers;

use Mark\MjdCore\Http\Controller;
use App\Models\User;
use Mark\MjdCore\Http\Session;
use Mark\MjdCore\Http\View;

class HomeController extends Controller
{
    public function index()
    {
        return $this->view('home');
    }

    public function documentation()
    {
        return $this->view('docs', [
            'title' => 'MJD-Core Documentation'
        ]);
    }
}