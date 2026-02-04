<?php

namespace App\Controllers;

use Mark\MjdCore\Http\Controller;
use Mark\MjdCore\Http\Session;
use Mark\MjdCore\Http\Request;
use App\Models\User;
use App\Services\MailService;
use Mark\MjdCore\Http\View;

class RegisterController extends Controller
{

    public function show()
    {
        return $this->view('register', ['title' => 'Create Account']);
    }

    public function store(Request $request, MailService $mailer)
    {
        $data = $request->all();
        $token = bin2hex(random_bytes(32));

        $existing = User::where('email', $data['email'])->first();
        if ($existing) {
            Session::flash('error', '[ERR]: Email already mapped to an identity.');
            return $this->redirect('/register');
        }

        User::create([
            'username'           => $data['username'],
            'email'              => $data['email'],
            'password'           => $data['password'],
            'verification_token' => $token,
            'is_verified'        => 0,
            'created_at'         => date('Y-m-d H:i:s')
        ]);

        $date = date('Y-m-d H:i:s T');
        $verificationUrl = $_ENV['APP_URL'] . "/verify-email?token=" . $token;
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN_NODE';

        $body = View::render('emails/verify', [
            'username' => $data['username'],
            'token' => $token,
            'date' => $date,
            'ip_address' => $ipAddress,
            'link'  => $verificationUrl
        ]);

        $mailer->send($data['email'], 'SYSTEM: Verification Required', $body);

        Session::flash('success', '[SYSTEM]: Account created. Check your inbox for activation link.');
        return $this->redirect('/login');
    }

    public function verify(Request $request)
    {
        $token = $request->input('token');

        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            Session::flash('error', '[ERR]: Invalid or expired verification token.');
            return $this->redirect('/register');
        }

        User::where('id', $user->id)->update([
            'is_verified'        => 1,
            'verification_token' => null
        ]);

        Session::flash('success', '[SYSTEM]: Identity verified. Access granted.');
        return $this->redirect('/login');
    }
}