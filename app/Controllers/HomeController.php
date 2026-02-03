<?php
namespace App\Controllers;

use Mark\MjdCore\Http\Controller;
use App\Models\User;

class HomeController extends Controller
{
    public function welcome()
    {
        return "HomeController index";
    }

    public function index()
    {
        return $this->view('home', [
            'title' => 'Home Page',
            'appName' => $_ENV['APP_NAME'],
            'username' => 'Mark'
        ]);
    }

    public function users()
    {
        $userModel = new User();
        $allUsers = $userModel->all();

        return $this->view('users', [
            'title' => 'User List',
            'users' => $allUsers
        ]);
    }
}