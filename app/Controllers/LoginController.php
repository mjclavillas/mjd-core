<?php
namespace App\Controllers;

use Mark\MjdCore\Http\Controller;
use Mark\MjdCore\Http\Session;
use App\Models\User;

class LoginController extends Controller
{
    public function show()
    {
        return $this->view('login', ['title' => 'Login']);
    }

    public function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        $userModel = new User();
        $user = $userModel->query()->where('email', $email)->first();

        if ($user && password_verify($password, $user->password)) {
            Session::set('user_id', $user->id);
            Session::set('username', $user->username);
            
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $user->query()->where('id', $user->id)
                            ->update(['remember_token' => hash('sha256', $token)]);
                setcookie('remember_me', $token, time() + (86400 * 30), "/", "", false, true);
            }

            return $this->redirect('/dashboard');
        }

        Session::flash('error', 'Invalid email or password.');
        return $this->redirect('login');
    }

    public function logout()
    {
        if (isset($_COOKIE['remember_me'])) {
            $user_id = Session::get('user_id');
            if ($user_id) {
                (new User())->query()->where('id', $user_id)->update(['remember_token' => null]);
            }
            setcookie('remember_me', '', time() - 3600, '/');
        }

        Session::destroy();
        return $this->redirect('login');
    }
}